<?php

namespace DuncanMcClean\SimpleCommerce\Orders;

use Statamic\Support\Traits\FluentlyGetsAndSets;

trait Calculable
{
    use FluentlyGetsAndSets;

    protected $grandTotal;
    protected $subTotal;
    protected $discountTotal;
    protected $taxTotal;
    protected $shippingTotal;

    public function grandTotal($grandTotal = null)
    {
        return $this->fluentlyGetOrSet('grandTotal')
            ->getter(fn ($grandTotal) => $grandTotal ?? 0)
            ->args(func_get_args());
    }

    public function subTotal($subTotal = null)
    {
        return $this->fluentlyGetOrSet('subTotal')
            ->getter(fn ($subTotal) => $subTotal ?? 0)
            ->args(func_get_args());
    }

    public function discountTotal($discountTotal = null)
    {
        return $this->fluentlyGetOrSet('discountTotal')
            ->getter(fn ($discountTotal) => $discountTotal ?? 0)
            ->args(func_get_args());
    }

    public function taxTotal($taxTotal = null)
    {
        return $this->fluentlyGetOrSet('taxTotal')
            ->getter(fn ($taxTotal) => $taxTotal ?? 0)
            ->args(func_get_args());
    }

    public function shippingTotal($shippingTotal = null)
    {
        return $this->fluentlyGetOrSet('shippingTotal')
            ->getter(fn ($shippingTotal) => $shippingTotal ?? 0)
            ->args(func_get_args());
    }

    public function recalculate(): self
    {
        // TODO

        return $this;
    }
}