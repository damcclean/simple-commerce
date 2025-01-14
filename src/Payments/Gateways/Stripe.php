<?php

namespace DuncanMcClean\SimpleCommerce\Payments\Gateways;

use DuncanMcClean\SimpleCommerce\Contracts\Cart\Cart;
use DuncanMcClean\SimpleCommerce\Contracts\Orders\Order;
use DuncanMcClean\SimpleCommerce\Facades;
use DuncanMcClean\SimpleCommerce\Orders\OrderStatus;
use DuncanMcClean\SimpleCommerce\SimpleCommerce;
use DuncanMcClean\SimpleCommerce\Support\QueuedClosure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Customer;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\WebhookSignature;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Stripe extends PaymentGateway
{
    public function name(): string
    {
        return __('Stripe');
    }

    public function __construct()
    {
        \Stripe\Stripe::setApiKey($this->config()->get('secret'));

        \Stripe\Stripe::setAppInfo(
            appName: 'Simple Commerce (Statamic)',
            appVersion: SimpleCommerce::version(),
            appUrl: 'https://statamic.com/addons/duncanmcclean/simple-commerce',
            appPartnerId: 'pp_partner_Jnvy4cdwcRmxfh'
        );

        if ($version = $this->config()->has('version')) {
            \Stripe\Stripe::setApiVersion($version);
        }
    }

    public function setup(Cart $cart): array
    {
        $stripeCustomerId = $cart->customer()?->get('stripe_customer_id');

        if ($cart->customer() && ! $stripeCustomerId) {
            $stripeCustomer = Customer::create([
                'name' => $cart->customer()->name(),
                'email' => $cart->customer()->email(),
            ]);

            $stripeCustomerId = $stripeCustomer->id;

            $cart->customer()->set('stripe_customer_id', $stripeCustomerId)->save();
        }

        if ($cart->get('stripe_payment_intent')) {
            $paymentIntent = PaymentIntent::update($cart->get('stripe_payment_intent'), [
                'amount' => $cart->grandTotal(),
                'customer' => $stripeCustomerId,
            ]);

            return ['client_secret' => $paymentIntent->client_secret];
        }

        $intentData = [
            'amount' => $cart->grandTotal(),
            'currency' => Str::lower($cart->site()->attribute('currency')),
            'customer' => $stripeCustomerId,
            'metadata' => ['cart_id' => $cart->id()],
            'automatic_payment_methods' => ['enabled' => true],
            'capture_method' => 'manual',
        ];

        $paymentIntent = PaymentIntent::create($intentData);

        $cart->set('stripe_payment_intent', $paymentIntent->id)->save();

        return ['client_secret' => $paymentIntent->client_secret];
    }

    public function afterRecalculating(Cart $cart): void
    {
        if ($cart->get('stripe_payment_intent')) {
            $this->setup($cart);
        }
    }

    public function process(Order $order): void
    {
        $order->set('payment_gateway', static::handle())->save();

        PaymentIntent::update($order->get('stripe_payment_intent'), [
            'description' => __('Order #:orderNumber', ['orderNumber' => $order->orderNumber()]),
            'metadata' => [
                'order_id' => $order->id(),
                'order_number' => $order->orderNumber(),
            ],
        ]);
    }

    public function rules(): array
    {
        return [
            'payment_intent' => ['required', 'starts_with:pi_'],
        ];
    }

    public function capture(Order $order)
    {
        $paymentIntent = PaymentIntent::retrieve($order->get('stripe_payment_intent'));

        $paymentIntent = $paymentIntent->capture([
            'amount_to_capture' => $order->grandTotal(),
        ]);

        if ($paymentIntent->status === 'succeeded') {
            $order->status(OrderStatus::PaymentReceived)->save();
        }
    }

    public function cancel(Cart $cart): void
    {
        $paymentIntent = PaymentIntent::retrieve($cart->get('stripe_payment_intent'));

        $paymentIntent->cancel();

        $cart->remove('stripe_payment_intent')->save();
    }

    public function webhook(Request $request)
    {
        try {
            WebhookSignature::verifyHeader(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $this->config()->get('webhook_secret'),
                300
            );
        } catch (SignatureVerificationException $exception) {
            throw new AccessDeniedHttpException($exception->getMessage(), $exception);
        }

        if ($request->type === 'payment_intent.amount_capturable_updated') {
            $paymentIntent = PaymentIntent::retrieve($request->data['object']['id']);

            // We're queuing this logic so that we can release the job and retry later if
            // the order hasn't been created yet.
            QueuedClosure::dispatch(function ($job) use ($paymentIntent): void {
                $order = Facades\Order::query()->where('stripe_payment_intent', $paymentIntent->id)->first();

                if (! $order) {
                    $job->release(10);
                    return;
                }

                $this->__construct();
                $this->capture($order);
            });
        }

        if ($request->type === 'payment_intent.refunded') {
            // TODO: Refunds.
        }

        // TODO: handle manual captures via the Stripe Dashboard
    }

    public function refund(Order $order, int $amount)
    {
        // TODO: Refunds. (We probably want to store amount_refunded, reason, refunded at on the order)
        // TODO: Bearing in mind that the webhook will also update the order details after the refund has been processed.

        Refund::create([
            'payment_intent' => $order->get('stripe_payment_intent'),
        ]);
    }
}
