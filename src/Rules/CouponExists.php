<?php

namespace DoubleThreeDigital\SimpleCommerce\Rules;

use DoubleThreeDigital\SimpleCommerce\Exceptions\CouponNotFound;
use DoubleThreeDigital\SimpleCommerce\Facades\Coupon;
use Illuminate\Contracts\Validation\Rule;

class CouponExists implements Rule
{
    public function passes($attribute, $value)
    {
        $coupon = Coupon::findByCode($value);

        if (! $coupon) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return __('simple-commerce::messages.validation.coupon_exists');
    }
}
