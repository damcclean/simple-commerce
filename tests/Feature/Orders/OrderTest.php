<?php

namespace Tests\Feature\Orders;

use DuncanMcClean\SimpleCommerce\Facades\Cart;
use DuncanMcClean\SimpleCommerce\Facades\Order;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use PreventsSavingStacheItemsToDisk;

    #[Test]
    public function can_make_order_from_cart()
    {
        $cart = Cart::make()
            ->id('abc')
            ->lineItems([
                [
                    'product' => '123',
                    'quantity' => 1,
                    'total' => 2500,
                ],
            ])
            ->grandTotal(2500)
            ->subTotal(2500)
            ->set('foo', 'bar')
            ->set('baz', 'foobar');

        $order = Order::makeFromCart($cart);

        $this->assertInstanceOf(\DuncanMcClean\SimpleCommerce\Contracts\Orders\Order::class, $order);

        $this->assertEquals($cart->lineItems(), $order->lineItems());
        $this->assertEquals(2500, $order->grandTotal());
        $this->assertEquals(2500, $order->subTotal());
        $this->assertEquals(0, $order->discountTotal());
        $this->assertEquals(0, $order->taxTotal());
        $this->assertEquals(0, $order->shippingTotal());
        $this->assertEquals('bar', $order->get('foo'));
        $this->assertEquals('foobar', $order->get('baz'));
    }

    #[Test]
    public function can_generate_order_number()
    {
        Order::make()->orderNumber(1000)->save();
        Order::make()->orderNumber(1001)->save();
        Order::make()->orderNumber(1002)->save();

        $order = tap(Order::make())->save();

        $this->assertEquals(1003, $order->orderNumber());
    }
}
