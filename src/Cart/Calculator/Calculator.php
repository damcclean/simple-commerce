<?php

namespace DuncanMcClean\SimpleCommerce\Cart\Calculator;

use DuncanMcClean\SimpleCommerce\Contracts\Cart\Cart;
use Illuminate\Support\Facades\Pipeline;

class Calculator
{
    public static function calculate(Cart $cart): Cart
    {
        return Pipeline::send($cart)
            ->through([
                ResetTotals::class,
                CalculateLineItems::class,
                ApplyCouponDiscounts::class,
                ApplyShipping::class,
                CalculateTaxes::class,
                CalculateTotals::class,
            ])
            ->thenReturn();
    }
}
