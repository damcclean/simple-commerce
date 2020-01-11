<?php

namespace Damcclean\Commerce\Http\Controllers\Web;

use Damcclean\Commerce\Events\AddedToCart;
use Damcclean\Commerce\Facades\Product;
use Damcclean\Commerce\Helpers\Cart;
use Damcclean\Commerce\Http\Requests\CartDeleteRequest;
use Damcclean\Commerce\Http\Requests\CartStoreRequest;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public $cartId;

    public function __construct(Request $request)
    {
        $this->cart = new Cart();
    }

    public function store(CartStoreRequest $request)
    {
        $this->createCart($request);

        $validate = $request->validated();

        $this->cart->add($this->cartId, [
            'product' => $request->product,
            'variant' => $request->variant,
            'quantity' => $request->quantity,
        ]);

        return back()
            ->with('success', 'Added to cart.');
    }

    public function destroy(CartDeleteRequest $request)
    {
        $validate = $request->validated();

        $this->cart->remove($request->slug);

        return redirect()
            ->back()
            ->with('message', 'Removed product from cart.');
    }

    protected function createCart(Request $request)
    {
        if (! $request->session()->get('commerce_cart_id')) {
            $request->session()->put('commerce_cart_id', $this->cart->create());
            $request->session()->save();
        }

        $this->cartId = $request->session()->get('commerce_cart_id');
    }
}
