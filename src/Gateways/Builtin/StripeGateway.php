<?php

namespace DoubleThreeDigital\SimpleCommerce\Gateways\Builtin;

use DoubleThreeDigital\SimpleCommerce\Contracts\Gateway;
use DoubleThreeDigital\SimpleCommerce\Contracts\Order as OrderContract;
use DoubleThreeDigital\SimpleCommerce\Exceptions\StripePaymentIntentNotProvided;
use DoubleThreeDigital\SimpleCommerce\Exceptions\StripeSecretMissing;
use DoubleThreeDigital\SimpleCommerce\Facades\Currency;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Gateways\BaseGateway;
use DoubleThreeDigital\SimpleCommerce\Gateways\Prepare;
use DoubleThreeDigital\SimpleCommerce\Gateways\Purchase;
use DoubleThreeDigital\SimpleCommerce\Gateways\Response as GatewayResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Statamic\Facades\Addon;
use Statamic\Facades\Site;
use Stripe\Customer as StripeCustomer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Stripe;

class StripeGateway extends BaseGateway implements Gateway
{
    public function name(): string
    {
        return 'Stripe';
    }

    public function prepare(Prepare $data): GatewayResponse
    {
        $this->setUpWithStripe();

        $order = $data->order();

        $intentData = [
            'amount'             => $order->get('grand_total'),
            'currency'           => Currency::get(Site::current())['code'],
            'description'        => "Order: {$order->title()}",
            'setup_future_usage' => 'off_session',
            'metadata'           => [
                'order_id' => $order->id,
            ],
        ];

        $customer = $order->customer();

        if ($customer && $customer->has('email')) {
            $stripeCustomerData = [
                'name'  => $customer->has('name') ? $customer->get('name') : 'Unknown',
                'email' => $customer->get('email'),
            ];

            $stripeCustomer = StripeCustomer::create($stripeCustomerData);
            $intentData['customer'] = $stripeCustomer->id;
        }

        if ($customer && $this->config()->has('receipt_email') && $this->config()->get('receipt_email') === true) {
            $intentData['receipt_email'] = $customer->email();
        }

        $intent = PaymentIntent::create($intentData);

        return new GatewayResponse(true, [
            'intent'         => $intent->id,
            'client_secret'  => $intent->client_secret,
        ]);
    }

    public function purchase(Purchase $data): GatewayResponse
    {
        $this->setUpWithStripe();

        $paymentIntent = PaymentIntent::retrieve($data->stripe()['intent']);
        $paymentMethod = PaymentMethod::retrieve($data->request()->payment_method);

        if ($paymentIntent->status === 'succeeded') {
            $data->order()->markAsPaid();
        }

        return new GatewayResponse(true, [
            'id'       => $paymentMethod->id,
            'object'   => $paymentMethod->object,
            'card'     => $paymentMethod->card->toArray(),
            'customer' => $paymentMethod->customer,
            'livemode' => $paymentMethod->livemode,
        ]);
    }

    public function purchaseRules(): array
    {
        return [
            'payment_method' => 'required|string',
        ];
    }

    public function getCharge(OrderContract $order): GatewayResponse
    {
        $this->setUpWithStripe();

        $paymentIntent = isset($order->get('stripe')['intent'])
            ? $order->get('stripe')['intent']
            : null;

        if (!$paymentIntent) {
            throw new StripePaymentIntentNotProvided('Stripe: No Payment Intent was provided to fetch.');
        }

        $charge = PaymentIntent::retrieve($paymentIntent);

        return new GatewayResponse(true, $charge->toArray());
    }

    public function refundCharge(OrderContract $order): GatewayResponse
    {
        $this->setUpWithStripe();

        $paymentIntent = isset($order->get('stripe')['intent'])
            ? $order->get('stripe')['intent']
            : null;

        if (!$paymentIntent) {
            throw new StripePaymentIntentNotProvided('Stripe: No Payment Intent was provided to action a refund.');
        }

        $refund = Refund::create([
            'payment_intent' => $paymentIntent,
        ]);

        return new GatewayResponse(true, $refund->toArray());
    }

    public function webhook(Request $request)
    {
        $this->setUpWithStripe();

        $payload = json_decode($request->getContent(), true);
        $method = 'handle'.Str::studly(str_replace('.', '_', $payload['type']));

        if ($method === 'handlePaymentIntentSucceeded') {
            $order = Order::find($payload['metadata']['order_id']);

            $order->markAsPaid();

            return new Response('Webhook handled', 200);
        }

        if ($method === 'handlePaymentIntentPaymentFailed') {
            // Email the customer
        }

        if ($method === 'handlePaymentIntentProcessing') {
            // Wait?
        }

        if ($method === 'handlePaymentIntentAmountCapturableUpdated') {
            // Cool, thanks Stripe?
        }

        return new Response();
    }

    protected function setUpWithStripe()
    {
        if (!$this->config()->has('secret')) {
            throw new StripeSecretMissing("Could not find your Stripe Secret. Please ensure it's added to your gateway configuration.");
        }

        Stripe::setApiKey($this->config()->get('secret'));

        try {
            Stripe::setAppInfo(
                'Statamic Simple Commerce',
                Addon::get('doublethreedigital/simple-commerce')->version(),
                'https://github.com/doublethreedigital/simple-commerce',
                'pp_partner_Jnvy4cdwcRmxfh'
            );
        } catch (\Exception $e) {
            Log::info('[Simple Commerce] Stripe: Failed to `setAppInfo`');
        }

        if ($version = $this->config()->has('version')) {
            Stripe::setApiVersion($version);
        }
    }
}
