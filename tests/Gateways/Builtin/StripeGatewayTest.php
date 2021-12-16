<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Gateways\Builtin;

use DoubleThreeDigital\SimpleCommerce\Facades\Customer;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use DoubleThreeDigital\SimpleCommerce\Gateways\Builtin\StripeGateway;
use DoubleThreeDigital\SimpleCommerce\Gateways\Prepare;
use DoubleThreeDigital\SimpleCommerce\Gateways\Purchase;
use DoubleThreeDigital\SimpleCommerce\Gateways\Response as GatewayResponse;
use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Statamic\Facades\Collection;
use Stripe\Customer as StripeCustomer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class StripeGatewayTest extends TestCase
{
    public StripeGateway $gateway;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new StripeGateway([
            'secret' => env('STRIPE_SECRET'),
        ]);

        Collection::make('orders')->title('Order')->save();
    }

    /** @test */
    public function has_a_name()
    {
        $name = $this->gateway->name();

        $this->assertIsString($name);
        $this->assertSame('Stripe', $name);
    }

    /** @test */
    public function can_prepare()
    {
        if (!env('STRIPE_SECRET')) {
            $this->markTestSkipped('Skipping, no Stripe Secret has been defined for this environment.');
        }

        $product = Product::create(['title' => 'Concert Ticket', 'price' => 5500]);

        $prepare = $this->gateway->prepare(new Prepare(
            new Request(),
            $order = Order::create([
                'items' => [
                    [
                        'id'       => app('stache')->generateId(),
                        'product'  => $product->id,
                        'quantity' => 1,
                        'total'    => 5500,
                        'metadata' => [],
                    ],
                ],
                'grand_total' => 5500,
                'title'       => '#0001',
            ])
        ));

        $this->assertIsObject($prepare);
        $this->assertTrue($prepare instanceof GatewayResponse);

        $this->assertTrue($prepare->success());
        $this->assertArrayHasKey('intent', $prepare->data());
        $this->assertArrayHasKey('client_secret', $prepare->data());

        $paymentIntent = PaymentIntent::retrieve($prepare->data()['intent']);

        $this->assertSame($paymentIntent->id, $prepare->data()['intent']);
        $this->assertSame($paymentIntent->amount, $order->get('grand_total'));
        $this->assertNull($paymentIntent->customer);
        $this->assertNull($paymentIntent->receipt_email);
    }

    /** @test */
    public function can_prepare_with_customer()
    {
        if (!env('STRIPE_SECRET')) {
            $this->markTestSkipped('Skipping, no Stripe Secret has been defined for this environment.');
        }

        $product = Product::create(['title' => 'Theatre Ticket', 'price' => 1299]);
        $customer = Customer::create(['name' => 'George', 'email' => 'george@example.com']);

        $prepare = $this->gateway->prepare(new Prepare(
            new Request(),
            $order = Order::create([
                'items' => [
                    [
                        'id'       => app('stache')->generateId(),
                        'product'  => $product->id,
                        'quantity' => 1,
                        'total'    => 1299,
                        'metadata' => [],
                    ],
                ],
                'grand_total' => 1299,
                'title'       => '#0002',
                'customer'    => $customer->id(),
            ])
        ));

        $this->assertIsObject($prepare);
        $this->assertTrue($prepare instanceof GatewayResponse);

        $this->assertTrue($prepare->success());
        $this->assertArrayHasKey('intent', $prepare->data());
        $this->assertArrayHasKey('client_secret', $prepare->data());

        $paymentIntent = PaymentIntent::retrieve($prepare->data()['intent']);

        $this->assertSame($paymentIntent->id, $prepare->data()['intent']);
        $this->assertSame($paymentIntent->amount, $order->get('grand_total'));
        $this->assertNotNull($paymentIntent->customer);
        $this->assertNull($paymentIntent->receipt_email);

        $stripeCustomer = StripeCustomer::retrieve($paymentIntent->customer);

        $this->assertSame($stripeCustomer->id, $paymentIntent->customer);
        $this->assertSame($stripeCustomer->name, 'George');
        $this->assertSame($stripeCustomer->email, 'george@example.com');
    }

    /** @test */
    public function can_prepare_with_receipt_email()
    {
        if (!env('STRIPE_SECRET')) {
            $this->markTestSkipped('Skipping, no Stripe Secret has been defined for this environment.');
        }

        $product = Product::create(['title' => 'Talent Show Ticket', 'price' => 1299]);
        $customer = Customer::create(['name' => 'George', 'email' => 'george@example.com']);

        $this->gateway->setConfig([
            'secret'        => env('STRIPE_SECRET'),
            'receipt_email' => true,
        ]);

        $prepare = $this->gateway->prepare(new Prepare(
            new Request(),
            $order = Order::create([
                'items' => [
                    [
                        'id'       => app('stache')->generateId(),
                        'product'  => $product->id,
                        'quantity' => 1,
                        'total'    => 1299,
                        'metadata' => [],
                    ],
                ],
                'grand_total' => 1299,
                'title'       => '#0003',
                'customer'    => $customer->id(),
            ])
        ));

        $this->assertIsObject($prepare);
        $this->assertTrue($prepare instanceof GatewayResponse);

        $this->assertTrue($prepare->success());
        $this->assertArrayHasKey('intent', $prepare->data());
        $this->assertArrayHasKey('client_secret', $prepare->data());

        $paymentIntent = PaymentIntent::retrieve($prepare->data()['intent']);

        $this->assertSame($paymentIntent->id, $prepare->data()['intent']);
        $this->assertSame($paymentIntent->amount, $order->get('grand_total'));
        $this->assertNotNull($paymentIntent->customer);
        $this->assertSame($paymentIntent->receipt_email, $customer->email());

        $stripeCustomer = StripeCustomer::retrieve($paymentIntent->customer);

        $this->assertSame($stripeCustomer->id, $paymentIntent->customer);
        $this->assertSame($stripeCustomer->name, 'George');
        $this->assertSame($stripeCustomer->email, 'george@example.com');
    }

    /** @test */
    public function can_purchase()
    {
        if (!env('STRIPE_SECRET')) {
            $this->markTestSkipped('Skipping, no Stripe Secret has been defined for this environment.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $product = Product::create(['title' => 'Zoo Ticket', 'price' => 1234]);

        $order = Order::create([
            'items' => [
                [
                    'id'       => app('stache')->generateId(),
                    'product'  => $product->id,
                    'quantity' => 1,
                    'total'    => 1234,
                    'metadata' => [],
                ],
            ],
            'grand_total' => 1234,
            'title'       => '#0004',
            'stripe'      => [
                'intent' => $paymentIntent = PaymentIntent::create([
                    'amount'   => 1234,
                    'currency' => 'GBP',
                ])->id,
            ],
        ]);

        $paymentMethod = PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number'    => '4242424242424242',
                'exp_month' => 7,
                'exp_year'  => 2022,
                'cvc'       => '314',
            ],
        ]);

        PaymentIntent::retrieve($paymentIntent)->confirm([
            'payment_method' => $paymentMethod->id,
        ]);

        $request = new Request(['payment_method' => $paymentMethod->id]);

        $purchase = $this->gateway->purchase(new Purchase($request, $order));

        $this->assertIsObject($purchase);
        $this->assertTrue($purchase instanceof GatewayResponse);

        $this->assertSame($purchase->data()['id'], $paymentMethod->id);
        $this->assertSame($purchase->data()['object'], $paymentMethod->object);
        // $this->assertSame($purchase->data()['card'], $paymentMethod->card);
        $this->assertSame($purchase->data()['customer'], $paymentMethod->customer);
        $this->assertSame($purchase->data()['livemode'], $paymentMethod->livemode);

        $order = $order->fresh();

        $this->assertTrue($order->get('is_paid'));
        $this->assertNotNull($order->get('paid_date'));
    }

    /** @test */
    public function has_purchase_rules()
    {
        $rules = (new StripeGateway())->purchaseRules();

        $this->assertIsArray($rules);

        $this->assertSame([
            'payment_method' => 'required|string',
        ], $rules);
    }

    /** @test */
    public function can_get_charge()
    {
        if (!env('STRIPE_SECRET')) {
            $this->markTestSkipped('Skipping, no Stripe Secret has been defined for this environment.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $order = Order::create([
            'stripe' => [
                'intent' => $paymentIntent = PaymentIntent::create([
                    'amount'   => 1234,
                    'currency' => 'GBP',
                ])->id,
            ],
            'grand_total' => 1234,
        ]);

        $charge = $this->gateway->getCharge($order);

        $this->assertIsObject($charge);
        $this->assertTrue($charge instanceof GatewayResponse);
        $this->assertTrue($charge->success());

        $this->assertSame($charge->data()['id'], $paymentIntent);
    }

    /** @test */
    public function can_refund_charge()
    {
        if (!env('STRIPE_SECRET')) {
            $this->markTestSkipped('Skipping, no Stripe Secret has been defined for this environment.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $order = Order::create([
            'stripe' => [
                'intent' => $paymentIntent = PaymentIntent::create([
                    'amount'   => 1234,
                    'currency' => 'GBP',
                ])->id,
            ],
            'grand_total' => 1234,
        ]);

        $paymentMethod = PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number'    => '4242424242424242',
                'exp_month' => 7,
                'exp_year'  => 2022,
                'cvc'       => '314',
            ],
        ]);

        PaymentIntent::retrieve($paymentIntent)->confirm([
            'payment_method' => $paymentMethod->id,
        ]);

        $refundCharge = $this->gateway->refundCharge($order);

        $this->assertIsObject($refundCharge);
        $this->assertTrue($refundCharge instanceof GatewayResponse);
        $this->assertTrue($refundCharge->success());

        $this->assertStringContainsString('re_', $refundCharge->data()['id']);
        $this->assertSame($refundCharge->data()['amount'], 1234);
        $this->assertSame($refundCharge->data()['payment_intent'], $paymentIntent);
    }

    /** @test */
    public function can_hit_webhook_with_payment_intent_succeeded_event()
    {
        $order = Order::create();

        $payload = [
            'type'     => 'payment_intent.succeeded',
            'metadata' => [
                'order_id' => $order->id(),
            ],
        ];

        $webhook = $this->gateway->webhook(new Request([], [], [], [], [], [], json_encode($payload)));

        $this->assertTrue($webhook instanceof Response);
    }
}
