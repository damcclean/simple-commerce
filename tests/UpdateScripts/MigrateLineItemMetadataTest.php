<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\UpdateScripts;

use DoubleThreeDigital\SimpleCommerce\Tests\SetupCollections;
use DoubleThreeDigital\SimpleCommerce\Tests\RunsUpdateScripts;
use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;
use DoubleThreeDigital\SimpleCommerce\UpdateScripts\MigrateLineItemMetadata;
use Statamic\Facades\Entry;

class MigrateLineItemMetadataTest extends TestCase
{
    use RunsUpdateScripts, SetupCollections;

    /** @test */
    public function it_migrates_metadata_of_order_line_items()
    {
        $this->setupOrders();

        $order = Entry::make()
            ->collection('orders')
            ->data([
                'items' => [
                    [
                        'id'          => 'idee-of-item',
                        'product'     => 'idee-of-product',
                        'total'       => 5,
                        'quantity'    => 1,
                        'product_key' => 'a-b-c',
                    ],
                ],
            ]);

        $order->save();

        $this->runUpdateScript(MigrateLineItemMetadata::class);

        $order->fresh();

        $this->assertArrayHasKey('id', $order->get('items')[0]);
        $this->assertArrayHasKey('product', $order->get('items')[0]);
        $this->assertArrayHasKey('total', $order->get('items')[0]);
        $this->assertArrayHasKey('quantity', $order->get('items')[0]);

        $this->assertArrayHasKey('metadata', $order->get('items')[0]);
        $this->assertArrayNotHasKey('product_key', $order->get('items')[0]);

        $this->assertArrayHasKey('product_key', $order->get('items')[0]['metadata']);
    }
}
