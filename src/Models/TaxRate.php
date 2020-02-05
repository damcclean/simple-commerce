<?php

namespace DoubleThreeDigital\SimpleCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = [
        'country_id', 'state_id', 'start_of_zip_code', 'rate', 'name', 'uuid',
    ];

    protected $appends = [
        'updateUrl', 'deleteUrl',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function cartTax()
    {
        return $this->hasMany(CartTax::class);
    }

    public function getUpdateUrlAttribute()
    {
        return cp_route('commerce-api.tax-rates.update', ['rate' => $this->attributes['uuid']]);
    }

    public function getDeleteUrlAttribute()
    {
        return cp_route('commerce-api.tax-rates.destroy', ['rate' => $this->attributes['uuid']]);
    }
}
