<?php

namespace DuncanMcClean\SimpleCommerce\Orders;

class Blueprint
{
    public static function getBlueprint(): \Statamic\Fields\Blueprint
    {
        // todo: allow configuring some of the blueprint fields
        // todo: read only status for line items field should depend on order status (ensure the calculations are re-done though)

        return \Statamic\Facades\Blueprint::makeFromTabs([
            'details' => [
                'display' => 'Details',
                'fields' => [
                    'order_number' => [
                        'type' => 'text',
                        'display' => 'Order Number',
                        'visibility' => 'hidden',
                        'listable' => true,
                    ],
                    'customer' => [ // todo
                        'type' => 'text',
                        'display' => 'Customer',
                        'instructions' => 'The customer who placed the order.',
                    ],
                    'line_items' => [
                        'type' => 'line_items',
                        'display' => 'Line Items',
                        'listable' => false,
                        'fields' => [
                            ['handle' => 'id', 'field' => ['type' => 'hidden']],
                            ['handle' => 'product', 'field' => ['type' => 'entries', 'max_items' => 1, 'display' => 'Product', 'collection' => 'products']], // todo: make the collection configurable
                            ['handle' => 'variant', 'field' => ['type' => 'text', 'display' => 'Variant']],
                            ['handle' => 'quantity', 'field' => ['type' => 'integer', 'display' => 'Quantity']],
                            ['handle' => 'total', 'field' => ['type' => 'money', 'display' => 'Total', 'visibility' => 'read_only']],
                            ['handle' => 'metadata', 'field' => ['type', 'array']],
                        ],
                    ],
                    'grand_total' => ['type' => 'money', 'display' => 'Grand Total', 'visibility' => 'read_only', 'listable' => true],
                    'sub_total' => ['type' => 'money', 'display' => 'Sub Total', 'visibility' => 'read_only'],
                    'discount_total' => ['type' => 'money', 'display' => 'Discount Total', 'visibility' => 'read_only'],
                    'tax_total' => ['type' => 'money', 'display' => 'Tax Total', 'visibility' => 'read_only'],
                    'shipping_total' => ['type' => 'money', 'display' => 'Shipping Total', 'visibility' => 'read_only'],
                    'payment_gateway' => ['type' => 'text', 'display' => 'Payment Gateway'], // todo: select options
                    'payment_data' => ['type' => 'array', 'display' => 'Payment Data'],
                    'shipping_method' => ['type' => 'text', 'display' => 'Shipping Method'], // todo: select options
                ],
            ],
            'sidebar' => [
                'fields' => [
                    'status' => [
                        'type' => 'select',
                        'display' => 'Status',
                        'options' => collect(OrderStatus::cases())->pluck('name', 'value')->all(),
                        'visibility' => 'read_only',
                        'listable' => true,
                    ],
                    'payment_status' => [
                        'type' => 'select',
                        'display' => 'Payment Status',
                        'options' => collect(PaymentStatus::cases())->pluck('name', 'value')->all(),
                        'visibility' => 'read_only',
                        'listable' => true,
                    ],
                ],
            ],
        ]);
    }
}
