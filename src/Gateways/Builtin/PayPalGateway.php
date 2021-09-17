<?php

namespace DoubleThreeDigital\SimpleCommerce\Gateways\Builtin;

use DoubleThreeDigital\SimpleCommerce\Contracts\Gateway;
use DoubleThreeDigital\SimpleCommerce\Contracts\Order;
use DoubleThreeDigital\SimpleCommerce\Events\PostCheckout;
use DoubleThreeDigital\SimpleCommerce\Exceptions\GatewayDoesNotSupportPurchase;
use DoubleThreeDigital\SimpleCommerce\Exceptions\PayPalDetailsMissingOnOrderException;
use DoubleThreeDigital\SimpleCommerce\Facades\Currency;
use DoubleThreeDigital\SimpleCommerce\Facades\Customer;
use DoubleThreeDigital\SimpleCommerce\Facades\Order as OrderFacade;
use DoubleThreeDigital\SimpleCommerce\Gateways\BaseGateway;
use DoubleThreeDigital\SimpleCommerce\Gateways\Prepare;
use DoubleThreeDigital\SimpleCommerce\Gateways\Purchase;
use DoubleThreeDigital\SimpleCommerce\Gateways\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;
use Statamic\Facades\Site;

class PayPalGateway extends BaseGateway implements Gateway
{
    protected $paypalClient;

    public function name(): string
    {
        return 'PayPal';
    }

    public function isOffsiteGateway(): bool
    {
        return true;
    }

    public function prepare(Prepare $data): Response
    {
        $this->setupPayPal();

        $order = $data->order();

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'value'    => (string) substr_replace($order->get('grand_total'), '.', -2, 0),
                        'currency_code' => Currency::get(Site::current())['code'],
                    ],
                    'description' => "Order {$order->title()}",
                    'custom_id'   => $order->id(),
                ],
            ],
            'application_context' => [
                'cancel_url' => $this->callbackUrl([
                    '_order_id' => $order->id(),
                ]),
                'return_url' => $this->callbackUrl([
                    '_order_id' => $order->id(),
                ]),
            ],
        ];

        /** @var \PayPalHttp\HttpResponse $response */
        $response = $this->paypalClient->execute($request);

        $checkoutUrl = collect($response->result->links)
            ->where('rel', 'approve')
            ->first();

        return new Response(true, [
            'result' => json_decode(json_encode($response->result), true),
        ], $checkoutUrl->href);
    }

    public function purchase(Purchase $data): Response
    {
        // We don't actually do anything here as PayPal is an
        // off-site gateway, so it has it's own checkout page.

        throw new GatewayDoesNotSupportPurchase("Gateway [paypal] does not support the `purchase` method.");
    }

    public function purchaseRules(): array
    {
        // PayPal is off-site, therefore doesn't use the traditional
        // checkout process provided by Simple Commerce. Hence why no rules
        // are defined here.

        return [];
    }

    public function getCharge(Order $order): Response
    {
        $this->setupPayPal();

        $paypalOrder = $order->get('paypal')['result'];

        $request = new OrdersGetRequest($paypalOrder['id']);

        /** @var \PayPalHttp\HttpResponse $response */
        $response = $this->paypalClient->execute($request);

        return new Response(true, json_decode(json_encode($response->result), true));
    }

    public function refundCharge(Order $order): Response
    {
        $this->setupPayPal();

        $request = new CapturesRefundRequest($order->get('gateway_data')['purchase_units'][0]['payments']['captures'][0]['id']);

        /** @var \PayPalHttp\HttpResponse $response */
        $response = $this->paypalClient->execute($request);

        return new Response(true, json_decode(json_encode($response->result), true));
    }

    // v2.4 TODO: Add this method to the contract
    public function callback(Request $request): bool
    {
        $this->setupPayPal();

        $order = OrderFacade::find($request->get('_order_id'));

        if (! $order) {
            return false;
        }

        $paypalOrder = optional($order->get('paypal'))['result'];

        if (! $paypalOrder) {
            throw new PayPalDetailsMissingOnOrderException("Order [{$order->id()}] does not have a PayPal Order ID.");
        }

        $request = new OrdersGetRequest($paypalOrder['id']);

        /** @var \PayPalHttp\HttpResponse $response */
        $response = $this->paypalClient->execute($request);

        return $response->result->status === 'APPROVED';
    }

    public function webhook(Request $request)
    {
        $this->setupPayPal();

        $payload = json_decode($request->getContent(), true);

        if ($payload['event_type'] === 'CHECKOUT.ORDER.APPROVED') {
            $order = OrderFacade::find($payload['resource']['purchase_units'][0]['custom_id']);

            $request = new OrdersCaptureRequest($payload['resource']['id']);

            /** @var \PayPalHttp\HttpResponse $response */
            $response = $this->paypalClient->execute($request);
            $responseBody = json_decode(json_encode($response->result), true);

            if (! $order->customer() && $responseBody['payer']['name'] || $responseBody['payer']['email_address']) {
                $customer = Customer::findByEmail($responseBody['payer']['email_address']);

                if (! $customer) {
                    $customer = Customer::create([
                        'name' => $responseBody['payer']['name']['given_name'] . ' ' . $responseBody['payer']['name']['surname'],
                        'email' => $responseBody['payer']['email_address'],
                    ]);
                }

                $order
                    ->customer($customer->id())
                    ->save();
            }

            if (! $order->shippingAddress() && isset($responseBody['purchase_units'][0]['shipping']['address'])) {
                $paypalShipping = $responseBody['purchase_units'][0]['shipping'];

                $order
                    ->set('shipping_name', $paypalShipping['name']['full_name'])
                    ->set('shipping_address', $paypalShipping['address']['address_line_1'])
                    ->set('shipping_city', $paypalShipping['address']['admin_area_1'])
                    ->set('shipping_country', $paypalShipping['address']['country_code'])
                    ->set('shipping_postal_code', $paypalShipping['address']['postal_code'])
                    ->save();
            }

            $order->set('gateway_data', $responseBody)->save();
            $order->markAsPaid();

            event(new PostCheckout($order));
        }

        return new HttpResponse();
    }

    protected function setupPayPal()
    {
        if ($this->config()->get('environment') === 'sandbox') {
            $environment = new SandboxEnvironment(
                $this->config()->get('client_id'),
                $this->config()->get('client_secret')
            );
        } else {
            $environment = new ProductionEnvironment(
                $this->config()->get('client_id'),
                $this->config()->get('client_secret')
            );
        }

        $this->paypalClient = new PayPalHttpClient($environment);
    }
}
