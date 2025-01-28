<?php

namespace DuncanMcClean\SimpleCommerce\Payments\Gateways;

use DuncanMcClean\SimpleCommerce\Contracts\Cart\Cart;
use DuncanMcClean\SimpleCommerce\Contracts\Orders\Order;
use DuncanMcClean\SimpleCommerce\Orders\LineItem;
use DuncanMcClean\SimpleCommerce\Shipping\ShippingOption;
use DuncanMcClean\SimpleCommerce\SimpleCommerce;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Mollie\Api\MollieApiClient;
use Statamic\Sites\Site;
use Statamic\Statamic;
use Statamic\Support\Arr;

class Mollie extends PaymentGateway
{
    private $mollie;

    public function __construct()
    {
        $this->mollie = new MollieApiClient;
        $this->mollie->setApiKey($this->config()->get('api_key'));

        $this->mollie->addVersionString('Statamic/'.Statamic::version());
        $this->mollie->addVersionString('SimpleCommerce/'.SimpleCommerce::version());

        Cache::rememberForever('Mollie_Organisation_Id', function () {
            $currentProfile = $this->mollie->profiles->getCurrent();
            $profileDashboardUrl = $currentProfile->_links->dashboard->href;

            return explode('/', parse_url($profileDashboardUrl, PHP_URL_PATH))[2];
        });
    }

    public function setup(Cart $cart): array
    {
        // todo: ensure the existing payment has the correct totals, if not, they should be updated.
//        if ($cart->get('mollie_payment_id')) {
//            $payment = $this->mollie->payments->get($cart->get('mollie_payment_id'));
//
//            return ['checkout_url' => $payment->getCheckoutUrl()];
//        }

//        dd($cart);
//

        $payment = $this->mollie->payments->create([
            'description' => "Pending Order: {$cart->id()}",
            'amount' => $this->formatAmount(site: $cart->site(), amount: $cart->grandTotal()),
            'redirectUrl' => $this->checkoutUrl(),
            //            'webhookUrl' => $this->webhookUrl(),
            'lines' => $cart->lineItems()
                ->map(function (LineItem $lineItem) use ($cart) {
                    // Mollie expects the unit price to include taxes. However, we only apply taxes to the line item total.
                    // So, we need to do some calculations to figure out what the unit price would be including tax.
                    $unitPrice = ($lineItem->total() + $lineItem->get('discount_amount', 0)) / $lineItem->quantity();

                    return [
                        'type' => 'physical',
                        'description' => $lineItem->product()->get('title'),
                        'quantity' => $lineItem->quantity(),
                        'unitPrice' => $this->formatAmount(site: $cart->site(), amount: $unitPrice),
                        'discountAmount' => $lineItem->has('discount_amount')
                            ? $this->formatAmount(site: $cart->site(), amount: $lineItem->get('discount_amount'))
                            : null,
                        'totalAmount' => $this->formatAmount(site: $cart->site(), amount: $lineItem->total()),
                        'vatRate' => collect($lineItem->get('tax_breakdown'))->sum('rate'),
                        'vatAmount' => $this->formatAmount(site: $cart->site(), amount: $lineItem->taxTotal()),
                        'productUrl' => $lineItem->product()->absoluteUrl(),
                    ];
                })
                ->when($cart->shippingOption(), function ($lines, ShippingOption $shippingOption) use ($cart) {
                    return $lines->push([
                        'type' => 'shipping_fee',
                        'description' => $shippingOption->name(),
                        'quantity' => 1,
                        'unitPrice' => $this->formatAmount(site: $cart->site(), amount: $cart->shippingTotal()),
                        'totalAmount' => $this->formatAmount(site: $cart->site(), amount: $cart->shippingTotal()),
                        'vatRate' => collect($cart->get('shipping_tax_breakdown'))->sum('rate'),
                        'vatAmount' => $this->formatAmount(site: $cart->site(), amount: $cart->get('shipping_tax_total', 0)),
                    ]);
                })
                ->values()->all(),
            'billingAddress' => $cart->hasBillingAddress() ? array_filter([
                'streetAndNumber' => $cart->billingAddress()?->line1,
                'streetAdditional' => $cart->billingAddress()?->line2,
                'postalCode' => $cart->billingAddress()?->postcode,
                'city' => $cart->billingAddress()?->city,
                'country' => Arr::get($cart->billingAddress()?->country()?->data(), 'iso2'),
            ]) : null,
            'shippingAddress' => $cart->hasShippingAddress() ? array_filter([
                'streetAndNumber' => $cart->shippingAddress()?->line1,
                'streetAdditional' => $cart->shippingAddress()?->line2,
                'postalCode' => $cart->shippingAddress()?->postcode,
                'city' => $cart->shippingAddress()?->city,
                'country' => Arr::get($cart->shippingAddress()?->country()?->data(), 'iso2'),
            ]) : null,
            'locale' => $cart->site()->locale(),
            'metadata' => [
                'cart_id' => $cart->id(),
            ],
            // todo: customer
        ]);

        $cart->set('mollie_payment_id', $payment->id)->save();

        return ['checkout_url' => $payment->getCheckoutUrl()];
    }

    public function process(Order $order): void
    {
        $order->set('payment_gateway', static::handle())->save();

        // todo: update payment description to make it more useful
        // todo: add order info to payment metadata
    }

    public function capture(Order $order): void
    {
        throw new \Exception("Mollie automatically captures *most* payments. They don't need captured manually.");
    }

    public function cancel(Cart $cart): void
    {
        // todo: check if the payment CAN be cancelled. if so, cancel it.
        // todo: if it can't be cancelled, refund it.
    }

    public function webhook(Request $request): Response
    {
        // todo: verify webhook signature
        // todo: handle webhook events (update statuses)
    }

    public function refund(Order $order, int $amount): void
    {
        // todo: refund the payment
    }

    // todo: logo
    // todo: fieldtype details

    private function formatAmount(Site $site, int $amount): array
    {
        return [
            'currency' => Str::upper($site->attribute('currency')),
            'value' => (string) number_format($amount / 100, 2, '.', ''),
        ];
    }
}
