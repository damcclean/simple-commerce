<?php

use Faker\Generator as Faker;
use DoubleThreeDigital\SimpleCommerce\Models\Country;
use Statamic\Stache\Stache;

$factory->define(Country::class, function (Faker $faker) {
    return [
        'name'  => $faker->country,
        'iso'   => $faker->countryISOAlpha3,
        'uuid'  => (new Stache)->generateId(),
    ];
});
