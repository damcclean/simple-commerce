<?php

namespace DuncanMcClean\SimpleCommerce\Stache\Stores;

use DuncanMcClean\SimpleCommerce\Contracts\Orders\Order as OrderContract;
use DuncanMcClean\SimpleCommerce\Facades\Order;
use Statamic\Entries\GetDateFromPath;
use Statamic\Entries\GetSlugFromPath;
use Statamic\Facades\YAML;
use Statamic\Stache\Stores\BasicStore;
use Statamic\Support\Arr;

class OrdersStore extends BasicStore
{
    protected $storeIndexes = [
        'order_number', 'date', 'cart', 'customer',
    ];

    public function key()
    {
        return 'orders';
    }

    public function getItemKey($item)
    {
        return $item->id();
    }

    public function makeItemFromFile($path, $contents): OrderContract
    {
        $data = YAML::file($path)->parse($contents);

        return Order::make()
            ->id(Arr::pull($data, 'id'))
            ->orderNumber((new GetSlugFromPath)($path))
            ->date((new GetDateFromPath)($path))
            ->cart(Arr::pull($data, 'cart'))
            ->status(Arr::pull($data, 'status'))
            ->customer(Arr::pull($data, 'customer'))
            ->coupon(Arr::pull($data, 'coupon'))
            ->lineItems(Arr::pull($data, 'line_items'))
            ->grandTotal(Arr::pull($data, 'grand_total'))
            ->subTotal(Arr::pull($data, 'sub_total'))
            ->discountTotal(Arr::pull($data, 'discount_total'))
            ->taxTotal(Arr::pull($data, 'tax_total'))
            ->shippingTotal(Arr::pull($data, 'shipping_total'))
            ->data($data);
    }
}
