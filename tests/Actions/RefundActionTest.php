<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Actions;

use DoubleThreeDigital\SimpleCommerce\Actions\RefundAction;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderStatus;
use DoubleThreeDigital\SimpleCommerce\Orders\PaymentStatus;
use DoubleThreeDigital\SimpleCommerce\Tests\Helpers\SetupCollections;
use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Stache;

class RefundActionTest extends TestCase
{
    use SetupCollections;

    public $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = new RefundAction();
    }

    /** @test */
    public function is_visible_to_paid_and_non_refunded_order()
    {
        $this->markTestSkipped();

        $order = Order::make()->paymentStatus(PaymentStatus::Paid);
        $order->save();

        $action = $this->action->visibleTo($order->resource());

        $this->assertTrue($action);
    }

    /** @test */
    public function is_not_visible_to_unpaid_orders()
    {
        $this->markTestSkipped();

        $order = Order::make()->paymentStatus(PaymentStatus::Paid);
        $order->save();

        $action = $this->action->visibleTo($order->resource());

        $this->assertFalse($action);
    }

    /** @test */
    public function is_not_visible_to_already_refunded_orders()
    {
        $this->markTestSkipped();

        $order = Order::make()->paymentStatus(PaymentStatus::Refunded);

        $order->save();

        $action = $this->action->visibleTo($order->resource());

        $this->assertFalse($action);
    }

    /** @test */
    public function is_not_visible_to_products()
    {
        $this->markTestSkipped();

        $product = Product::make()
            ->price(1200)
            ->data([
                'title' => 'Medium Jumper',
            ]);

        $product->save();

        $action = $this->action->visibleTo($product->resource());

        $this->assertFalse($action);
    }

    /** @test */
    public function is_not_able_to_be_run_in_bulk()
    {
        $this->markTestSkipped();

        $order = Order::make()->paymentStatus(PaymentStatus::Refunded);

        $order->save();

        $action = $this->action->visibleToBulk([$order->resource()]);

        $this->assertFalse($action);
    }

    /** @test */
    public function order_can_be_refunded()
    {
        Collection::make('orders')->save();

        $order = Entry::make()
            ->collection('orders')
            ->id(Stache::generateId())
            ->merge([
                'payment_status' => PaymentStatus::Paid,
                'gateway' => [
                    'use' => 'DoubleThreeDigital\SimpleCommerce\Gateways\Builtin\DummyGateway',
                    'data' => [
                        'id' => '123456789abcdefg',
                    ],
                ],
            ]);

        $order->save();

        $this->action->run([$order], null);

        $order->fresh();

        $this->assertSame($order->data()->get('payment_status'), 'refunded');
        $this->assertArrayHasKey('refund', $order->data()->get('gateway'));
    }
}
