<?php

namespace DoubleThreeDigital\SimpleCommerce\Support\Rules;

use DoubleThreeDigital\SimpleCommerce\Support\Countries;
use Illuminate\Contracts\Validation\Rule;

class CountryExists implements Rule
{
    public function passes($attribute, $value)
    {
        $matchesIso = collect(Countries::toArray())->filter(function ($country) use ($value) {
            return $country['iso'] == $value;
        });

        return $matchesIso->count() >= 1;
    }

    public function message()
    {
        return __('simple-commerce::messages.validation.country_exists');
    }
}
