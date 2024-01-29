<?php

use Carbon\Carbon;
use DoubleThreeDigital\SimpleCommerce\Contracts\Order as OrderContract;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use DoubleThreeDigital\SimpleCommerce\Orders\EloquentQueryBuilder;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderModel;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderStatus;
use DoubleThreeDigital\SimpleCommerce\Orders\PaymentStatus;
use DoubleThreeDigital\SimpleCommerce\Tests\Helpers\SetupCollections;
use DoubleThreeDigital\SimpleCommerce\Tests\Helpers\UseDatabaseContentDrivers;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(SetupCollections::class);
uses(RefreshDatabase::class);
uses(UseDatabaseContentDrivers::class);

it('can get all orders', function () {
    $productOne = Product::make()->price(1000);
    $productOne->save();

    $productTwo = Product::make()->price(1000);
    $productTwo->save();

    OrderModel::create([
        'order_number' => 1002,
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
        'order_number' => 1003,
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

    $orders = Order::all();

    expect($orders->count())->toBe(2);
    expect($orders->map->orderNumber()->toArray())->toBe([1002, 1003]);
});

it('can query orders', function () {
    $productOne = Product::make()->price(1000);
    $productOne->save();

    $productTwo = Product::make()->price(1000);
    $productTwo->save();

    OrderModel::create([
        'order_number' => 1002,
        'order_status' => OrderStatus::Cart->value,
        'payment_status' => PaymentStatus::Unpaid->value,
        'items' => [
            ['product' => $productOne->id(), 'quantity' => 1, 'total' => 1000],
        ],
        'data' => [
            'foo' => 'bar',
            'status_log' => [
                ['status' => 'cart', 'timestamp' => Carbon::parse('2024-01-27 15:00:00')->timestamp, 'data' => []],
            ],
        ],
    ]);

    OrderModel::create([
        'order_number' => 1003,
        'order_status' => OrderStatus::Placed->value,
        'payment_status' => PaymentStatus::Paid->value,
        'items' => [
            ['product' => $productTwo->id(), 'quantity' => 1, 'total' => 1000],
        ],
        'data' => [
            'boo' => 'foo',
            'status_log' => [
                ['status' => 'placed', 'timestamp' => Carbon::parse('2024-01-27 15:00:00')->timestamp, 'data' => []],
                ['status' => 'paid', 'timestamp' => Carbon::parse('2024-01-27 17:55:00')->timestamp, 'data' => []],
            ],
        ],
    ]);

    OrderModel::create([
        'order_number' => 1004,
        'order_status' => OrderStatus::Dispatched->value,
        'payment_status' => PaymentStatus::Paid->value,
        'items' => [
            ['product' => $productOne->id(), 'quantity' => 1, 'total' => 1000],
            ['product' => $productTwo->id(), 'quantity' => 1, 'total' => 1000],
        ],
        'data' => [
            'baz' => 'fax',
            'status_log' => [
                ['status' => 'placed', 'timestamp' => Carbon::parse('2024-01-27 15:00:00')->timestamp, 'data' => []],
                ['status' => 'paid', 'timestamp' => Carbon::parse('2024-01-27 15:00:00')->timestamp, 'data' => []],
                ['status' => 'dispatched', 'timestamp' => Carbon::parse('2024-01-29 12:12:12')->timestamp, 'data' => []],
            ],
        ],
    ]);

    // Ensure all 3 orders are returned when we're not doing any filtering.
    $query = Order::query();
    expect($query)->toBeInstanceOf(EloquentQueryBuilder::class);
    expect($query->count())->toBe(3);

    // Ensure a specific order is returned when we're filtering by ID.
    $query = Order::query()->where('order_number', 1002);
    expect($query->count())->toBe(1);
    expect($query->get()[0])->toBeInstanceOf(OrderContract::class);

    // Ensure we can filter by order status.
    $query = Order::query()->whereOrderStatus(OrderStatus::Cart);
    expect($query->count())->toBe(1);
    expect($query->get()[0])
        ->toBeInstanceOf(OrderContract::class)
        ->and($query->get()[0]->orderNumber())->toBe(1002);

    // Ensure we can filter by payment status.
    $query = Order::query()->wherePaymentStatus(PaymentStatus::Paid);
    expect($query->count())->toBe(2);
    expect($query->get()[0])
        ->toBeInstanceOf(OrderContract::class)
        ->and($query->get()[0]->orderNumber())->toBe(1003);
    expect($query->get()[1])
        ->toBeInstanceOf(OrderContract::class)
        ->and($query->get()[1]->orderNumber())->toBe(1004);

    // Query by status log timestamps
    // TODO: make this query not error out on sqlite & the count not be wrong on MySQL
    // $query = Order::query()->whereStatusLogDate(PaymentStatus::Paid, Carbon::parse('2024-01-27'));
    // expect($query->count())->toBe(2);
    // expect($query->get()[0])
    //     ->toBeInstanceOf(OrderContract::class)
    //     ->and($query->get()[0]->orderNumber())->toBe(1003);
    // expect($query->get()[1])
    //     ->toBeInstanceOf(OrderContract::class)
    //     ->and($query->get()[1]->orderNumber())->toBe(1004);
});

it('can find order', function () {
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

    expect($order->id)->toBe($find->id());
    expect(1)->toBe($find->lineItems()->count());
    expect('bar')->toBe($find->get('foo'));
});

it('can find order with custom column', function () {
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

    expect($order->id)->toBe($find->id());
    expect(1)->toBe($find->lineItems()->count());
    expect('bar')->toBe($find->get('foo'));
    expect('Yes')->toBe($find->get('ordered_on_tuesday'));
});

it('can create order', function () {
    $create = Order::make()
        ->status(OrderStatus::Placed)
        ->paymentStatus(PaymentStatus::Paid)
        ->grandTotal(1000);

    $create->save();

    $this->assertNotNull($create->id());
    expect(OrderStatus::Placed)->toBe($create->status());
    expect(PaymentStatus::Paid)->toBe($create->paymentStatus());
    expect(1000)->toBe($create->grandTotal());
});

it('can save order', function () {
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

    expect($orderRecord->id)->toBe($order->id());
    expect(true)->toBe($order->get('is_special_order'));
});

it('can save order when bit of data has its own column', function () {
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

    expect($orderRecord->id)->toBe($order->id());
    expect('Yes')->toBe($order->get('ordered_on_tuesday'));

    $this->assertDatabaseHas('orders', [
        'id' => $orderRecord->id,
        'ordered_on_tuesday' => 'Yes',
    ]);
});

it('can delete order', function () {
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
});
