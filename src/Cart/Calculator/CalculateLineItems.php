<?php

namespace DuncanMcClean\SimpleCommerce\Cart\Calculator;

use Closure;
use DuncanMcClean\SimpleCommerce\Cart\Cart;
use DuncanMcClean\SimpleCommerce\Orders\LineItem;
use DuncanMcClean\SimpleCommerce\Products\ProductType;
use Statamic\Support\Traits\Hookable;

class CalculateLineItems
{
    protected static $priceHook;

    public function handle(Cart $cart, Closure $next)
    {
        $cart->lineItems()->map(function (LineItem $lineItem) use ($cart) {
            $product = $lineItem->product();

            $price = match (true) {
                isset(static::$priceHook) => (static::$priceHook)($cart, $lineItem),
                $product->type() === ProductType::Product => $product->price(),
                $product->type() === ProductType::Variant => $product->variant($lineItem->variant()->key())->price(),
            };

            $lineItem->unitPrice($price);
            $lineItem->subTotal($price * $lineItem->quantity());
            $lineItem->total($lineItem->subTotal());

            return $lineItem;
        });

        return $next($cart);
    }

    public static function priceHook(?Closure $closure)
    {
        static::$priceHook = $closure;

        return new static;
    }
}
