<?php

namespace DoubleThreeDigital\SimpleCommerce;

use Closure;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;
use Statamic\Statamic;

class SimpleCommerce
{
    /** @var array */
    protected static $gateways = [];
    protected static $shippingMethods = [];

    /** @var Contracts\TaxEngine */
    protected static $taxEngine;

    public static $productPriceHook;
    public static $productVariantPriceHook;

    public static function bootGateways()
    {
        return Statamic::booted(function () {
            foreach (config('simple-commerce.gateways') as $class => $config) {
                if ($class) {
                    $class = str_replace('::class', '', $class);

                    static::$gateways[] = [
                        $class,
                        $config,
                    ];
                }
            }

            return new static();
        });
    }

    public static function gateways(): array
    {
        return collect(static::$gateways)
            ->map(function ($gateway) {
                /** @var Contracts\Gateway $instance */
                $instance = new $gateway[0]();

                return [
                    'name'            => $instance->name(),
                    'handle'          => $handle = Str::of($instance->name())->camel()->lower()->__toString(),
                    'class'           => $gateway[0],
                    'formatted_class' => addslashes($gateway[0]),
                    'display'         => isset($gateway[1]['display']) ? $gateway[1]['display'] : $instance->name(),
                    'purchaseRules'   => $instance->purchaseRules(),
                    'gateway-config'  => $gateway[1],
                    'webhook_url'     => Str::finish(config('app.url'), '/').config('statamic.routes.action').'/simple-commerce/gateways/'.$handle.'/webhook',
                ];
            })
            ->toArray();
    }

    public static function registerGateway(string $gateway, array $config = [])
    {
        static::$gateways[] = [
            $gateway,
            $config,
        ];
    }

    public static function bootTaxEngine()
    {
        return Statamic::booted(function () {
            static::$taxEngine = config('simple-commerce.tax_engine');
        });
    }

    public static function bootShippingMethods()
    {
        return Statamic::booted(function () {
            foreach (config('simple-commerce.sites') as $siteHandle => $value) {
                if (! isset($value['shipping']['methods'])) {
                    continue;
                }

                static::$shippingMethods[$siteHandle] = $value['shipping']['methods'];
            }

            return new static();
        });
    }

    public static function setTaxEngine($taxEngine)
    {
        static::$taxEngine = $taxEngine;
    }

    public static function taxEngine(): Contracts\TaxEngine
    {
        return new static::$taxEngine;
    }

    public static function isUsingStandardTaxEngine(): bool
    {
        // TODO: figure out how we can actually set the engine for a specific test
        if (app()->environment('testing')) {
            return true;
        }

        return static::taxEngine() instanceof Tax\Standard\TaxEngine;
    }

    public static function shippingMethods(string $site = null)
    {
        if ($site) {
            return collect(static::$shippingMethods[$site] ?? []);
        }

        return collect(static::$shippingMethods[Site::default()->handle()] ?? []);
    }

    public static function registerShippingMethod(string $site, string $shippingMethod)
    {
        static::$shippingMethods[$site][] = $shippingMethod;
    }

    public static function freshOrderNumber()
    {
        $minimum = config('simple-commerce.minimum_order_number');

        $query = Collection::find(SimpleCommerce::orderDriver()['collection'])
            ->queryEntries()
            ->orderBy('title', 'asc')
            ->where('title', '!=', null)
            ->get()
            ->map(function ($order) {
                $order->title = str_replace('Order ', '', $order->title);
                $order->title = str_replace('#', '', $order->title);

                return $order->title;
            })
            ->last();

        if (! $query) {
            return $minimum + 1;
        }

        return ((int) $query) + 1;
    }

    public static function orderDriver(): array
    {
        return config('simple-commerce.content.orders');
    }

    public static function productDriver(): array
    {
        return config('simple-commerce.content.products');
    }

    public static function couponDriver(): array
    {
        return config('simple-commerce.content.coupons');
    }

    public static function customerDriver(): array
    {
        return config('simple-commerce.content.customers');
    }

    /**
     * This shouldn't be used as a Statamic::svg() replacement. It's only useful for grabbing
     * icons from Simple Commerce's `resources/svgs` directory.
     */
    public static function svg($name)
    {
        if (File::exists(__DIR__.'/../resources/svg/'.$name.'.svg')) {
            return File::get(__DIR__.'/../resources/svg/'.$name.'.svg');
        }

        return Statamic::svg($name);
    }

    public static function productPriceHook(Closure $callback): self
    {
        static::$productPriceHook = $callback;

        return new static;
    }

    public static function productVariantPriceHook(Closure $callback): self
    {
        static::$productVariantPriceHook = $callback;

        return new static;
    }
}
