<?php

namespace DuncanMcClean\SimpleCommerce\Events;

use DuncanMcClean\SimpleCommerce\Contracts\Orders\Order;
use Statamic\Contracts\Git\ProvidesCommitMessage;

class OrderSaved implements ProvidesCommitMessage
{
    public function __construct(public Order $order)
    {
    }

    public function commitMessage()
    {
        return __('Order saved', [], config('statamic.git.locale'));
    }
}