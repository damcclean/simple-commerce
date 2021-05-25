<?php

namespace DoubleThreeDigital\SimpleCommerce\Filters;

use DoubleThreeDigital\SimpleCommerce\Orders\Order;
use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use Statamic\Query\Scopes\Filter;

class OrderStatusFilter extends Filter
{
    public $pinned = true;
    public static $title = 'Order Status';

    public function fieldItems()
    {
        return [
            'type' => [
                'type' => 'radio',
                'options' => [
                    'cart' => 'Cart',
                    'order' => 'Order',
                ],
            ],
        ];
    }

    public function autoApply()
    {
        return [
            'type' => 'order',
        ];
    }

    public function apply($query, $values)
    {
        $query
            ->where('is_paid', $values['type'] === 'order');
    }

    public function badge($values)
    {
        $orderStatusLabel = $this->fieldItems()['type']['options'][$values['type']];

        return "Order Status: {$orderStatusLabel}";
    }

    public function visibleTo($key)
    {
        return $key === 'entries'
            && SimpleCommerce::orderDriver()['driver'] === Order::class
            && $this->context['collection'] === SimpleCommerce::orderDriver()['collection'];
    }
}
