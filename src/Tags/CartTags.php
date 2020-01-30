<?php

namespace DoubleThreeDigital\SimpleCommerce\Tags;

use DoubleThreeDigital\SimpleCommerce\Helpers\Cart;
use DoubleThreeDigital\SimpleCommerce\Helpers\Currency;
use Statamic\Tags\Tags;

class CartTags extends Tags
{
    public $cart;
    public $cartId;

    protected static $handle = 'cart';

    public function __construct()
    {
        $this->cart = new Cart();

        $this->createCart();
    }

    public function index()
    {
        return $this->cart->get($this->cartId);
    }

    public function items()
    {
        return $this->index();
    }

    public function shipping()
    {
        return $this->cart->getShipping($this->cartId);
    }

    public function count()
    {
        return $this->cart->count($this->cartId);
    }

    public function total()
    {
        return (new Currency())->parse($this->cart->total($this->cartId), true, true);
    }

    protected function createCart()
    {
        if (! request()->session()->get('commerce_cart_id')) {
            request()->session()->put('commerce_cart_id', $this->cart->create());
            request()->session()->save();
        }

        $this->cartId = request()->session()->get('commerce_cart_id');
    }
}
