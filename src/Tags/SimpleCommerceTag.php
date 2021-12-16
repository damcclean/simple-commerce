<?php

namespace DoubleThreeDigital\SimpleCommerce\Tags;

use DoubleThreeDigital\SimpleCommerce\Support\Countries;
use DoubleThreeDigital\SimpleCommerce\Support\Currencies;
use DoubleThreeDigital\SimpleCommerce\Support\Regions;
use Statamic\Tags\TagNotFoundException;
use Statamic\Tags\Tags;

class SimpleCommerceTag extends Tags
{
    protected static $handle = 'sc';
    protected static $aliases = ['simple-commerce'];

    protected $tagClasses = [
        'cart'     => CartTags::class,
        'checkout' => CheckoutTags::class,
        'coupon'   => CouponTags::class,
        'customer' => CustomerTags::class,
        'gateways' => GatewayTags::class,
        'shipping' => ShippingTags::class,
    ];

    public function wildcard(string $tag)
    {
        $tag = explode(':', $tag);

        $class = collect($this->tagClasses)
            ->map(function ($value, $key) {
                return [
                    'key'   => $key,
                    'value' => $value,
                ];
            })
            ->where('key', $tag[0])
            ->pluck('value')
            ->first();

        $method = isset($tag[1]) ? $tag[1] : 'index';

        if (!$class) {
            throw new TagNotFoundException("Tag [{$tag[0]}] could not be found.");
        }

        if (method_exists($class, $method)) {
            return (new $class($this))->{$method}();
        }

        if (method_exists($class, 'wildcard')) {
            return (new $class($this))->wildcard($method);
        }

        throw new TagNotFoundException("Tag [{$tag[0]}] could not be found.");
    }

    public function countries()
    {
        $countries = Countries::map(function ($country) {
            return array_merge($country, [
                'regions' => Regions::findByCountry($country)->toArray(),
            ]);
        })->values();

        if ($inclusions = $this->params->explode('only', [])) {
            $countries = $countries
                ->filter(function ($country) use ($inclusions) {
                    return in_array($country['iso'], $inclusions)
                        || in_array($country['name'], $inclusions);
                })->sortBy(function ($country) use ($inclusions) {
                    return array_search($country['iso'], $inclusions);
                });
        } else {
            if ($exclusions = $this->params->explode('exclude', [])) {
                $countries = $countries->filter(function ($country) use ($exclusions) {
                    return !(in_array($country['iso'], $exclusions)
                        || in_array($country['name'], $exclusions));
                });
            }
            if ($common = $this->params->explode('common', [])) {
                $commonCountries = $countries
                    ->filter(function ($country) use ($common) {
                        return in_array($country['iso'], $common)
                            || in_array($country['name'], $common);
                    })->sortBy(function ($country) use ($common) {
                        return array_search($country['iso'], $common);
                    });

                $commonCountries->push([
                    'iso'  => '',
                    'name' => '-',
                ]);

                $countries = $commonCountries->concat($countries->filter(function ($country) use ($common) {
                    return !(in_array($country['iso'], $common) || in_array($country['name'], $common));
                }));
            }
        }

        return $countries->toArray();
    }

    public function currencies()
    {
        return Currencies::toArray();
    }

    public function regions()
    {
        return Regions::map(function ($region) {
            return array_merge($region, [
                'country' => Countries::findByRegion($region)->first(),
            ]);
        })->toArray();
    }

    public function errors()
    {
        if (!$this->hasErrors()) {
            return null;
        }

        $errors = [];

        foreach (session('errors')->getBag('simple-commerce')->all() as $error) {
            $errors[]['value'] = $error;
        }

        return $this->parseLoop($errors);
    }

    public function hasErrors()
    {
        return session()->has('errors');
    }
}
