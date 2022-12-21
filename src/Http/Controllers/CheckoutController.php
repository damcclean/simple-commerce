<?php

namespace DoubleThreeDigital\SimpleCommerce\Http\Controllers;

use DoubleThreeDigital\SimpleCommerce\Events\PostCheckout;
use DoubleThreeDigital\SimpleCommerce\Events\PreCheckout;
use DoubleThreeDigital\SimpleCommerce\Exceptions\CheckoutProductHasNoStockException;
use DoubleThreeDigital\SimpleCommerce\Exceptions\CustomerNotFound;
use DoubleThreeDigital\SimpleCommerce\Exceptions\GatewayNotProvided;
use DoubleThreeDigital\SimpleCommerce\Exceptions\PreventCheckout;
use DoubleThreeDigital\SimpleCommerce\Facades\Coupon;
use DoubleThreeDigital\SimpleCommerce\Facades\Customer;
use DoubleThreeDigital\SimpleCommerce\Facades\Gateway;
use DoubleThreeDigital\SimpleCommerce\Http\Requests\AcceptsFormRequests;
use DoubleThreeDigital\SimpleCommerce\Http\Requests\Checkout\StoreRequest;
use DoubleThreeDigital\SimpleCommerce\Orders\Cart\Drivers\CartDriver;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderStatus;
use DoubleThreeDigital\SimpleCommerce\Orders\PaymentStatus;
use DoubleThreeDigital\SimpleCommerce\Rules\ValidCoupon;
use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Statamic\Facades\Site;
use Statamic\Sites\Site as SitesSite;

class CheckoutController extends BaseActionController
{
    use CartDriver, AcceptsFormRequests;

    public $cart;
    public StoreRequest $request;
    public $excludedKeys = ['_token', '_params', '_redirect', '_request'];

    public function __invoke(StoreRequest $request)
    {
        $this->cart = $this->getCart();
        $this->request = $request;

        try {
            $this
                ->preCheckout()
                ->handleValidation()
                ->handleCustomerDetails()
                ->handleCoupon()
                ->handleStock()
                ->handleRemainingData()
                ->handlePayment()
                ->postCheckout();

            $this->cart->updateOrderStatus(OrderStatus::Placed);
        } catch (CheckoutProductHasNoStockException $e) {
            $lineItem = $this->cart->lineItems()->filter(function ($lineItem) use ($e) {
                return $lineItem->product()->id() === $e->product->id();
            })->first();

            $this->cart->removeLineItem($lineItem->id());
            $this->cart->save();

            return $this->withErrors($this->request, __('Checkout failed. A product in your cart has no stock left. The product has been removed from your cart.'));
        } catch (PreventCheckout $e) {
            return $this->withErrors($this->request, $e->getMessage());
        }

        return $this->withSuccess($request, [
            'message' => __('simple-commerce.messages.checkout_complete'),
            'cart'    => $request->wantsJson()
                ? $this->cart->toResource()
                : $this->cart->toAugmentedArray(),
            'is_checkout_request' => true,
        ]);
    }

    protected function preCheckout()
    {
        event(new PreCheckout($this->cart, $this->request));

        return $this;
    }

    protected function handleValidation()
    {
        $rules = array_merge(
            $this->request->get('_request')
                ? $this->buildFormRequest($this->request->get('_request'), $this->request)->rules()
                : [],
            $this->request->has('gateway')
                ? Gateway::use($this->request->get('gateway'))->purchaseRules()
                : [],
            [
                'coupon' => ['nullable', new ValidCoupon($this->cart)],
                'email' => ['nullable', 'email', function ($attribute, $value, $fail) {
                    if (preg_match('/^\S*$/u', $value) === 0) {
                        return $fail(__('simple-commerce::validation.email_address_contains_spaces'));
                    }
                }],
            ],
        );

        $messages = array_merge(
            $this->request->get('_request')
                ? $this->buildFormRequest($this->request->get('_request'), $this->request)->messages()
                : [],
            $this->request->has('gateway')
                ? Gateway::use($this->request->get('gateway'))->purchaseMessages()
                : [],
            [],
        );

        $this->request->validate($rules, $messages);

        return $this;
    }

    protected function handleCustomerDetails()
    {
        $customerData = $this->request->has('customer')
            ? $this->request->get('customer')
            : [];

        if (is_string($customerData)) {
            $this->cart->customer($customerData);
            $this->cart->save();

            $this->excludedKeys[] = 'customer';

            return $this;
        }

        if ($this->request->has('name') && $this->request->has('email')) {
            $customerData['name'] = $this->request->get('name');
            $customerData['email'] = $this->request->get('email');

            $this->excludedKeys[] = 'name';
            $this->excludedKeys[] = 'email';
        } elseif ($this->request->has('first_name') && $this->request->has('last_name') && $this->request->has('email')) {
            $customerData['first_name'] = $this->request->get('first_name');
            $customerData['last_name'] = $this->request->get('last_name');
            $customerData['email'] = $this->request->get('email');

            $this->excludedKeys[] = 'first_name';
            $this->excludedKeys[] = 'last_name';
            $this->excludedKeys[] = 'email';
        } elseif ($this->request->has('email')) {
            $customerData['email'] = $this->request->get('email');

            $this->excludedKeys[] = 'email';
        }

        if (isset($customerData['email'])) {
            try {
                $customer = Customer::findByEmail($customerData['email']);
            } catch (CustomerNotFound $e) {
                $customerItemData = [
                    'published' => true,
                ];

                if (isset($customerData['name'])) {
                    $customerItemData['name'] = $customerData['name'];
                }

                if (isset($customerData['first_name']) && isset($customerData['last_name'])) {
                    $customerItemData['first_name'] = $customerData['first_name'];
                    $customerItemData['last_name'] = $customerData['last_name'];
                }

                $customer = Customer::make()
                    ->email($customerData['email'])
                    ->data($customerItemData);

                $customer->save();
            }

            $customer
                ->merge(
                    Arr::only($customerData, config('simple-commerce.field_whitelist.customers'))
                )
                ->save();

            $this->cart->customer($customer->id());
            $this->cart->save();

            $this->cart = $this->cart->fresh();
        }

        $this->excludedKeys[] = 'customer';

        return $this;
    }

    protected function handleStock()
    {
        $this->cart = app(Pipeline::class)
            ->send($this->cart)
            ->through([
                \DoubleThreeDigital\SimpleCommerce\Orders\Checkout\HandleStock::class,
            ])
            ->thenReturn();

        return $this;
    }

    protected function handleCoupon()
    {
        if ($coupon = $this->request->get('coupon')) {
            $coupon = Coupon::findByCode($coupon);

            $this->cart->coupon($coupon);
            $this->cart->save();

            $this->excludedKeys[] = 'coupon';
        }

        return $this;
    }

    protected function handleRemainingData()
    {
        $data = [];

        foreach (Arr::except($this->request->all(), $this->excludedKeys) as $key => $value) {
            if ($value === 'on') {
                $value = true;
            } elseif ($value === 'off') {
                $value = false;
            }

            $data[$key] = $value;
        }

        // We don't recommend doing this BUT if you want to you can override all the line items at the
        // last minute like this. We just need to ensure it's set correctly.
        if (isset($data['items'])) {
            $this->cart->lineItems($data['items']);

            unset($data['items']);
        }

        if ($data !== []) {
            $this->cart->merge(Arr::only($data, config('simple-commerce.field_whitelist.orders')))->save();
            $this->cart->save();

            $this->cart = $this->cart->fresh();
        }

        return $this;
    }

    protected function handlePayment()
    {
        $this->cart = $this->cart->recalculate();

        if ($this->cart->grandTotal() <= 0) {
            $this->cart->updatePaymentStatus(PaymentStatus::Paid);

            return $this;
        }

        if (! $this->request->has('gateway') && $this->cart->paymentStatus() !== PaymentStatus::Paid && $this->cart->grandTotal() !== 0) {
            throw new GatewayNotProvided('No gateway provided.');
        }

        $purchase = Gateway::use($this->request->gateway)->purchase($this->request, $this->cart);

        $this->excludedKeys[] = 'gateway';

        foreach (Gateway::use($this->request->gateway)->purchaseRules() as $key => $rule) {
            $this->excludedKeys[] = $key;
        }

        $this->cart->fresh();

        return $this;
    }

    protected function postCheckout()
    {
        if (! isset(SimpleCommerce::customerDriver()['model']) && $this->cart->customer()) {
            $this->cart->customer()->merge([
                'orders' => $this->cart->customer()->orders()
                    ->pluck('id')
                    ->push($this->cart->id())
                    ->toArray(),
            ]);

            $this->cart->customer()->save();
        }

        if (! $this->request->has('gateway') && $this->cart->status() !== PaymentStatus::Paid && $this->cart->grandTotal() === 0) {
            $this->cart->updatePaymentStatus(PaymentStatus::Paid);
        }

        if ($this->cart->coupon()) {
            $this->cart->coupon()->redeem();
        }

        $this->forgetCart();

        event(new PostCheckout($this->cart, $this->request));

        return $this;
    }

    protected function guessSiteFromRequest(): SitesSite
    {
        if ($site = request()->get('site')) {
            return Site::get($site);
        }

        foreach (Site::all() as $site) {
            if (Str::contains(request()->url(), $site->url())) {
                return $site;
            }
        }

        if ($referer = request()->header('referer')) {
            foreach (Site::all() as $site) {
                if (Str::contains($referer, $site->url())) {
                    return $site;
                }
            }
        }

        return Site::current();
    }
}
