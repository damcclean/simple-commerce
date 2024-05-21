<?php

namespace DuncanMcClean\SimpleCommerce\Orders;

use DuncanMcClean\SimpleCommerce\Money;
use Statamic\Data\AbstractAugmented;

class AugmentedOrder extends AbstractAugmented
{
    private $cachedKeys;

    public function keys(): array
    {
        if ($this->cachedKeys) {
            return $this->cachedKeys;
        }

        return $this->cachedKeys = $this->data->keys()
            ->merge($this->commonKeys())
            ->unique()->sort()->values()->all();
    }

    private function commonKeys(): array
    {
        return [
            'order_number',
            'status',
            'payment_status',
            'customer',
            'line_items',
            'grand_total',
            'sub_total',
            'discount_total',
            'tax_total',
            'shipping_total',
            'payment_gateway',
            'payment_data',
            'shipping_method',
        ];
    }

    // todo: status & payment status

    public function grandTotal(): string
    {
        return Money::format($this->data->grandTotal());
    }

    public function subTotal(): string
    {
        return Money::format($this->data->subTotal());
    }

    public function discountTotal(): string
    {
        return Money::format($this->data->discountTotal());
    }

    public function taxTotal(): string
    {
        return Money::format($this->data->taxTotal());
    }

    public function shippingTotal(): string
    {
        return Money::format($this->data->shippingTotal());
    }
}