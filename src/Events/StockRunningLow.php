<?php

namespace DuncanMcClean\SimpleCommerce\Events;

use DuncanMcClean\SimpleCommerce\Contracts\Products\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class StockRunningLow
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(public Product $product, public $variant, public int $stock)
    {
    }
}
