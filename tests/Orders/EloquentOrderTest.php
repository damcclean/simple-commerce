<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Orders;

use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderModel;
use DoubleThreeDigital\SimpleCommerce\Orders\PaymentStatus;
use DoubleThreeDigital\SimpleCommerce\Tests\Helpers\SetupCollections;
use DoubleThreeDigital\SimpleCommerce\Tests\Helpers\UseDatabaseContentDrivers;
use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class EloquentOrderTest extends TestCase
{
    use SetupCollections, RefreshDatabase, UseDatabaseContentDrivers;

    /** @test */
    public function can_get_all_orders()
    {
        $productOne = Product::make()->price(1000);
        $productOne->save();

        $productTwo = Product::make()->price(1000);
        $productTwo->save();

        OrderModel::create([
            'items' => [
                [
                    'product' => $productOne->id(),
                    'quantity' => 1,
                    'total' => 1000,
                ],
            ],
            'data' => [
                'foo' => 'bar',
            ],
        ]);

        OrderModel::create([
            'items' => [
                [
                    'product' => $productTwo->id(),
                    'quantity' => 1,
                    'total' => 1000,
                ],
            ],
            'data' => [
                'boo' => 'foo',
            ],
        ]);

        $all = Order::all();

        $this->assertTrue($all instanceof Collection);
        $this->assertSame($all->count(), 2);
    }

    /** @test */
    public function can_find_order()
    {
        $product = Product::make()->price(1000);
        $product->save();

        $order = OrderModel::create([
            'items' => [
                [
                    'product' => $product->id(),
                    'quantity' => 1,
                    'total' => 1000,
                ],
            ],
            'data' => [
                'foo' => 'bar',
            ],
        ]);

        $find = Order::find($order->id);

        $this->assertSame($find->id(), $order->id);
        $this->assertSame($find->lineItems()->count(), 1);
        $this->assertSame($find->get('foo'), 'bar');
    }

    /** @test */
    public function can_find_order_with_custom_column()
    {
        $product = Product::make()->price(1000);
        $product->save();

        $order = OrderModel::create([
            'items' => [
                [
                    'product' => $product->id(),
                    'quantity' => 1,
                    'total' => 1000,
                ],
            ],
            'data' => [
                'foo' => 'bar',
            ],
            'ordered_on_tuesday' => 'Yes',
        ]);

        $find = Order::find($order->id);

        $this->assertSame($find->id(), $order->id);
        $this->assertSame($find->lineItems()->count(), 1);
        $this->assertSame($find->get('foo'), 'bar');
        $this->assertSame($find->get('ordered_on_tuesday'), 'Yes');
    }

    /** @test */
    public function can_create()
    {
        $create = Order::make()
            ->paymentStatus(PaymentStatus::Paid)
            ->grandTotal(1000);

        $create->save();

        $this->assertNotNull($create->id());
        $this->assertSame($create->paymentStatus(), PaymentStatus::Paid);
        $this->assertSame($create->grandTotal(), 1000);
    }

    /** @test */
    public function can_save()
    {
        $product = Product::make()->price(1000);
        $product->save();

        $orderRecord = OrderModel::create([
            'items' => [
                [
                    'product' => $product->id(),
                    'quantity' => 1,
                    'total' => 1000,
                ],
            ],
            'data' => [
                'foo' => 'bar',
            ],
        ]);

        $order = Order::find($orderRecord->id);

        $order->set('is_special_order', true);

        $order->save();

        $this->assertSame($order->id(), $orderRecord->id);
        $this->assertSame($order->get('is_special_order'), true);
    }

    /** @test */
    public function can_save_when_bit_of_data_has_its_own_column()
    {
        $product = Product::make()->price(1000);
        $product->save();

        $orderRecord = OrderModel::create([
            'items' => [
                [
                    'product' => $product->id(),
                    'quantity' => 1,
                    'total' => 1000,
                ],
            ],
        ]);

        $order = Order::find($orderRecord->id);

        $order->set('ordered_on_tuesday', 'Yes');

        $order->save();

        $this->assertSame($order->id(), $orderRecord->id);
        $this->assertSame($order->get('ordered_on_tuesday'), 'Yes');

        $this->assertDatabaseHas('orders', [
            'id' => $orderRecord->id,
            'ordered_on_tuesday' => 'Yes',
        ]);
    }

    /** @test */
    public function can_delete()
    {
        $orderRecord = OrderModel::create([
            'payment_status' => 'paid',
            'grand_total' => 1000,
        ]);

        $order = Order::find($orderRecord->id);

        $order->delete();

        $this->assertDatabaseMissing('orders', [
            'id' => $orderRecord->id,
            'payment_status' => 'paid',
            'grand_total' => 1000,
        ]);
    }
}
