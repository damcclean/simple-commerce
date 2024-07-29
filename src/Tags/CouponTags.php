<?php

namespace DuncanMcClean\SimpleCommerce\Tags;

use DuncanMcClean\SimpleCommerce\Contracts\Coupon;
use DuncanMcClean\SimpleCommerce\Facades\Cart;

class CouponTags extends SubTag
{
    use Concerns\FormBuilder;

    public function index(): array
    {
        $coupon = $this->getCartCoupon();

        if (! $coupon) {
            return [];
        }

        return $coupon->toAugmentedArray();
    }

    public function has(): bool
    {
        return ! is_null($this->getCartCoupon());
    }

    public function redeem()
    {
        return $this->createForm(
            route('statamic.simple-commerce.coupon.store'),
            [],
            'POST'
        );
    }

    public function remove()
    {
        return $this->createForm(
            route('statamic.simple-commerce.coupon.destroy'),
            [],
            'DELETE'
        );
    }

    public function wildcard($method)
    {
        $coupon = $this->getCartCoupon();

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        if (property_exists($coupon, $method)) {
            return $coupon->{$method};
        }

        if (array_key_exists($method, $coupon->toAugmentedArray())) {
            return $coupon->toAugmentedArray()[$method];
        }

        if ($coupon->has($method)) {
            return $coupon->get($method);
        }

        return null;
    }

    protected function getCartCoupon(): ?Coupon
    {
        // todo: add coupons back to orders
        return null;

        if (! Cart::hasCurrentCart()) {
            return null;
        }

        $coupon = Cart::current()->coupon();

        if (! $coupon) {
            return null;
        }

        return $coupon;
    }
}
