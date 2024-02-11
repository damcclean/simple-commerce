<?php

namespace DuncanMcClean\SimpleCommerce\Customers;

use DuncanMcClean\SimpleCommerce\Facades\Customer;
use Statamic\Auth\Eloquent\UserQueryBuilder;

class EloquentUserQueryBuilder extends UserQueryBuilder
{
    protected function transform($items, $columns = ['*'])
    {
        return $items->map(fn ($item) => Customer::fromModel($item));
    }
}
