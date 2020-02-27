<?php

namespace DoubleThreeDigital\SimpleCommerce\Console\Commands;

use DoubleThreeDigital\SimpleCommerce\Models\Cart;
use DoubleThreeDigital\SimpleCommerce\Models\CartItem;
use DoubleThreeDigital\SimpleCommerce\Models\CartShipping;
use DoubleThreeDigital\SimpleCommerce\Models\CartTax;
use Illuminate\Console\Command;

class CartDeletionCommand extends Command
{
    protected $signature = 'commerce:cart-delete';

    public function __construct()
    {
        parent::__construct();

        $this->description = 'Deletes carts older than '.config('simple-commerce.cart-retention').' days old';
    }

    public function handle()
    {
        $this->info('Deleting old carts...');

        $this->deletion();
    }

    public function deletion()
    {
        Cart::whereDate('created_at', '<=', now()->subDays(config('simple-commerce.cart-retention'))->toDateString())
            ->each(function (Cart $cart) {
                CartItem::where('cart_id', $cart->id)
                    ->get()
                    ->each(function (CartItem $item) {
                        $item->delete();
                    });

                CartShipping::where('cart_id', $cart->id)
                    ->get()
                    ->each(function (CartShipping $item) {
                        $item->delete();
                    });

                CartTax::where('cart_id', $cart->id)
                    ->get()
                    ->each(function (CartTax $item) {
                        $item->delete();
                    });

                $cart->delete();
            });
    }
}
