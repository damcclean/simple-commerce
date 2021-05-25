<?php

namespace DoubleThreeDigital\SimpleCommerce\Actions;

use DoubleThreeDigital\SimpleCommerce\Facades\Gateway;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use Statamic\Actions\Action;
use Statamic\Entries\Entry;

class RefundAction extends Action
{
    public static function title()
    {
        return __('simple-commerce::messages.actions.refund');
    }

    public function visibleTo($item)
    {
        return $item instanceof Entry &&
            $item->collectionHandle() === SimpleCommerce::orderDriver()['collection'] &&
            ($item->data()->has('is_paid') && $item->data()->get('is_paid')) &&
            ($item->data()->get('is_refunded') === false || $item->data()->get('is_refunded') === null);
    }

    public function visibleToBulk($items)
    {
        return false;
    }

    public function run($items, $values)
    {
        collect($items)
            ->each(function ($entry) {
                $order = Order::find($entry->id());

                return Gateway::use($order->get('gateway'))->refundCharge($order);
            });
    }
}
