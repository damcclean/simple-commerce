<?php

namespace DoubleThreeDigital\SimpleCommerce\Customers;

use DoubleThreeDigital\Runway\Traits\HasRunwayResource;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerModel extends Model
{
    use HasFactory, HasRunwayResource;

    protected $table = 'customers';

    protected $guarded = [];

    protected $casts = [
        'data' => 'json',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(OrderModel::class, 'customer_id');
    }
}
