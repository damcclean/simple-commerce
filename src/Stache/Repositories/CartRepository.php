<?php

namespace DuncanMcClean\SimpleCommerce\Stache\Repositories;

use DuncanMcClean\SimpleCommerce\Contracts\Cart\CartRepository as RepositoryContract;
use DuncanMcClean\SimpleCommerce\Contracts\Cart\Cart;
use DuncanMcClean\SimpleCommerce\Contracts\Cart\QueryBuilder;
use DuncanMcClean\SimpleCommerce\Contracts\Orders\Order as OrderContract;
use DuncanMcClean\SimpleCommerce\Exceptions\CartNotFound;
use DuncanMcClean\SimpleCommerce\Facades\Cart as CartFacade;
use DuncanMcClean\SimpleCommerce\Facades\Order as OrderFacade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Statamic\Facades\Blink;
use Statamic\Facades\Site;
use Statamic\Stache\Stache;

class CartRepository implements RepositoryContract
{
    protected $stache;

    protected $store;

    public function __construct(Stache $stache)
    {
        $this->stache = $stache;
        $this->store = $stache->store('carts');
    }

    public function all()
    {
        return $this->query()->get();
    }

    public function query()
    {
        return app(QueryBuilder::class);
    }

    public function find($id): ?Cart
    {
        return $this->query()->where('id', $id)->first();
    }

    public function findOrFail($id): Cart
    {
        $cart = $this->find($id);

        if (! $cart) {
            throw new CartNotFound("Cart [{$id}] could not be found.");
        }

        return $cart;
    }

    public function current(): ?Cart
    {
        if (! $this->hasCurrentCart()) {
            return $this->make();
        }

        return CartFacade::find(Cookie::get($this->getKey()));
    }

    public function hasCurrentCart(): bool
    {
        return Blink::has($this->getKey()) || Cookie::has($this->getKey());
    }

    public function forgetCurrentCart(): void
    {
        $this->current()->delete();

        Cookie::queue(Cookie::forget($this->getKey()));
        Blink::forget($this->getKey());
    }

    private function getKey(): string
    {
        return 'simple-commerce-cart'; // TODO: Make this configurable.
    }

    public function make(): Cart
    {
        return app(Cart::class);
    }

    public function save(Cart $cart): void
    {
        if (! $cart->id()) {
            $cart->id($this->stache->generateId());
        }

        $this->store->save($cart);

        Cookie::queue($this->getKey(), $cart->id());

        // Because the cookie won't be set until the end of the request,
        // we need to set it somewhere for the remainder of the request.
        // And that somewhere is Blink.
        Blink::put($this->getKey(), $cart->id());
    }

    public function delete(Cart $cart): void
    {
        $this->store->delete($cart);
    }

    public static function bindings(): array
    {
        return [
            Cart::class => \DuncanMcClean\SimpleCommerce\Cart\Cart::class,
            QueryBuilder::class => \DuncanMcClean\SimpleCommerce\Stache\Query\CartQueryBuilder::class,
        ];
    }
}
