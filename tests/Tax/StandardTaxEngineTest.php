<?php

use DoubleThreeDigital\SimpleCommerce\Exceptions\PreventCheckout;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use DoubleThreeDigital\SimpleCommerce\Facades\TaxCategory;
use DoubleThreeDigital\SimpleCommerce\Facades\TaxRate;
use DoubleThreeDigital\SimpleCommerce\Facades\TaxZone;
use DoubleThreeDigital\SimpleCommerce\Tax\Standard\TaxEngine as StandardTaxEngine;
use DoubleThreeDigital\SimpleCommerce\Tests\Helpers\SetupCollections;
use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

uses(SetupCollections::class);
beforeEach(function () {
    try {
        collect(File::allFiles(base_path('content/simple-commerce/tax-categories')))
            ->each(function ($file) {
                File::delete($file);
            });

        collect(File::allFiles(base_path('content/simple-commerce/tax-rates')))
            ->each(function ($file) {
                File::delete($file);
            });

        collect(File::allFiles(base_path('content/simple-commerce/tax-zones')))
            ->each(function ($file) {
                File::delete($file);
            });
    } catch (DirectoryNotFoundException $e) {
        // That's fine...
    }
});


test('can correctly calculate tax rate based on country', function () {
    Config::set('simple-commerce.tax_engine', StandardTaxEngine::class);

    Config::set('simple-commerce.tax_engine_config', [
        'address' => 'billing',
    ]);

    $taxCategory = TaxCategory::make()
        ->id('standard-vat')
        ->name('Standard VAT');

    $taxCategory->save();

    $taxZone = TaxZone::make()
        ->id('uk')
        ->name('United Kingdom')
        ->country('GB');

    $taxZone->save();

    $taxRate = TaxRate::make()
        ->id('uk-20-vat')
        ->name('20% VAT')
        ->rate(20)
        ->category($taxCategory->id())
        ->zone($taxZone->id());

    $taxRate->save();

    $product = Product::make()
        ->price(1000)
        ->taxCategory($taxCategory->id())
        ->data([
            'title' => 'Cat Food',
        ]);

    $product->save();

    $order = Order::make()->lineItems([
        [
            'id' => app('stache')->generateId(),
            'product' => $product->id,
            'quantity' => 1,
            'total' => 1000,
        ],
    ])->merge([
        'billing_address' => '1 Test Street',
        'billing_country' => 'GB',
        'use_shipping_address_for_billing' => false,
    ]);

    $order->save();

    $recalculate = $order->recalculate();

    // Ensure tax on line items are right
    $this->assertSame($recalculate->lineItems()->first()->tax(), [
        'amount' => 167,
        'rate' => 20,
        'price_includes_tax' => false,
    ]);

    // Ensure global order tax is right
    expect(167)->toBe($recalculate->get('tax_total'));
});

test('can correctly calculate tax rate based on region', function () {
    Config::set('simple-commerce.tax_engine', StandardTaxEngine::class);

    Config::set('simple-commerce.tax_engine_config', [
        'address' => 'billing',
    ]);

    $taxCategory = TaxCategory::make()
        ->id('standard-vat')
        ->name('Standard VAT');

    $taxCategory->save();

    $taxZone = TaxZone::make()
        ->id('uk')
        ->name('United Kingdom')
        ->country('GB')
        ->region('gb-sct');

    $taxZone->save();

    // Just so we can tell this rate apart
    $taxRate = TaxRate::make()
        ->id('scottish-15-vat')
        ->name('15% Scottish VAT')
        ->rate(15)
        ->category($taxCategory->id())
        ->zone($taxZone->id());

    $taxRate->save();

    $product = Product::make()
        ->price(1000)
        ->taxCategory($taxCategory->id())
        ->data([
            'title' => 'Cat Food',
        ]);

    $product->save();

    $order = Order::make()->lineItems([
        [
            'id' => app('stache')->generateId(),
            'product' => $product->id,
            'quantity' => 1,
            'total' => 1000,
        ],
    ])->merge([
        'billing_address' => '1 Test Street',
        'billing_country' => 'GB',
        'billing_region' => 'gb-sct',
        'use_shipping_address_for_billing' => false,
    ]);

    $order->save();

    $recalculate = $order->recalculate();

    // Ensure tax on line items are right
    $this->assertSame($recalculate->lineItems()->first()->tax(), [
        'amount' => 130,
        'rate' => 15,
        'price_includes_tax' => false,
    ]);

    // Ensure global order tax is right
    expect(130)->toBe($recalculate->get('tax_total'));
});

test('can calculate tax rate when included in price', function () {
    Config::set('simple-commerce.tax_engine', StandardTaxEngine::class);

    Config::set('simple-commerce.tax_engine_config', [
        'address' => 'billing',
    ]);

    $taxCategory = TaxCategory::make()
        ->id('standard-vat')
        ->name('Standard VAT');

    $taxCategory->save();

    $taxZone = TaxZone::make()
        ->id('uk')
        ->name('United Kingdom')
        ->country('GB');

    $taxZone->save();

    $taxRate = TaxRate::make()
        ->id('uk-20-vat')
        ->name('20% VAT')
        ->rate(20)
        ->category($taxCategory->id())
        ->zone($taxZone->id())
        ->includeInPrice(true);

    $taxRate->save();

    $product = Product::make()
        ->price(1000)
        ->taxCategory($taxCategory->id())
        ->data([
            'title' => 'Cat Food',
        ]);

    $product->save();

    $order = Order::make()->lineItems([
        [
            'id' => app('stache')->generateId(),
            'product' => $product->id,
            'quantity' => 1,
            'total' => 1000,
        ],
    ])->merge([
        'billing_address' => '1 Test Street',
        'billing_country' => 'GB',
        'use_shipping_address_for_billing' => false,
    ]);

    $order->save();

    $recalculate = $order->recalculate();

    // Ensure tax on line items are right
    $this->assertSame($recalculate->lineItems()->first()->tax(), [
        'amount' => 167,
        'rate' => 20,
        'price_includes_tax' => true,
    ]);

    // Ensure global order tax is right
    expect(167)->toBe($recalculate->get('tax_total'));
});

test('can use default tax rate if no rate available', function () {
    Config::set('simple-commerce.tax_engine_config.behaviour.no_rate_available', 'default_rate');

    TaxCategory::make()
        ->id('standard-stuff')
        ->name('Standard Stuff')
        ->save();

    TaxCategory::make()
        ->id('default-category')
        ->name('Default')
        ->save();

    TaxRate::make()
        ->id('default-rate')
        ->name('Default')
        ->rate(12)
        ->includeInPrice(true)
        ->category('default-category')
        ->save();

    $product = Product::make()
        ->price(1000)
        ->taxCategory('standard-stuff')
        ->data([
            'title' => 'Cat Food',
        ]);

    $product->save();

    $order = Order::make()->lineItems([
        [
            'id' => app('stache')->generateId(),
            'product' => $product->id,
            'quantity' => 1,
            'total' => 1000,
        ],
    ])->merge([
        'billing_address' => '1 Test Street',
        'billing_country' => 'GB',
        'use_shipping_address_for_billing' => false,
    ]);

    $order->save();

    $recalculate = $order->recalculate();

    // Ensure the tax rate is correct on the line item
    expect(12)->toBe($recalculate->lineItems()->first()->tax()['rate']);

    // Ensure global order tax is right
    expect(107)->toBe($recalculate->get('tax_total'));
});

test('throws prevent checkout exception if no rate available', function () {
    // Ensure an exception is thrown during this test
    $this->expectException(PreventCheckout::class);

    Config::set('simple-commerce.tax_engine_config.behaviour.no_rate_available', 'prevent_checkout');

    TaxCategory::make()
        ->id('standard-stuff')
        ->name('Standard Stuff')
        ->save();

    TaxCategory::make()
        ->id('default-category')
        ->name('Default')
        ->save();

    TaxRate::make()
        ->id('default')
        ->name('Default')
        ->rate(12)
        ->includeInPrice(true)
        ->category('default-category')
        ->save();

    $product = Product::make()
        ->price(1000)
        ->taxCategory('standard-stuff')
        ->data([
            'title' => 'Cat Food',
        ]);

    $product->save();

    $order = Order::make()->lineItems([
        [
            'id' => app('stache')->generateId(),
            'product' => $product->id,
            'quantity' => 1,
            'total' => 1000,
        ],
    ])->merge([
        'billing_address' => '1 Test Street',
        'billing_country' => 'GB',
        'use_shipping_address_for_billing' => false,
    ]);

    $order->save();

    $order->recalculate();
});

test('uses default address if no address provided', function () {
    Config::set('simple-commerce.tax_engine_config.behaviour.no_address_provided', 'default_address');

    Config::set('simple-commerce.tax_engine_config.behaviour.default_address', [
        'address_line_1' => '',
        'address_line_2' => '',
        'city' => '',
        'region' => '',
        'country' => 'US',
        'zip_code' => '',
    ]);

    TaxCategory::make()
        ->id('standard-stuff')
        ->name('Standard Stuff')
        ->save();

    TaxZone::make()
        ->id('for-the-us')
        ->country('US')
        ->save();

    TaxRate::make()
        ->id('used-for-default-address')
        ->name('used for default address')
        ->rate(99)
        ->includeInPrice(true)
        ->category('standard-stuff')
        ->zone('for-the-us')
        ->save();

    $product = Product::make()
        ->price(1000)
        ->taxCategory('standard-stuff')
        ->data([
            'title' => 'Cat Food',
        ]);

    $product->save();

    $order = Order::make()->lineItems([
        [
            'id' => app('stache')->generateId(),
            'product' => $product->id,
            'quantity' => 1,
            'total' => 1000,
        ],
    ]);

    $order->save();

    $recalculate = $order->recalculate();

    // Ensure the tax rate is correct on the line item
    expect(99)->toBe($recalculate->lineItems()->first()->tax()['rate']);

    // Ensure global order tax is right
    expect(497)->toBe($recalculate->get('tax_total'));
});

test('throws prevent checkout exception if no address provided', function () {
    // Ensure an exception is thrown during this test
    $this->expectException(PreventCheckout::class);

    Config::set('simple-commerce.tax_engine_config.behaviour.no_address_provided', 'prevent_checkout');

    TaxCategory::make()
        ->id('standard-stuff')
        ->name('Standard Stuff')
        ->save();

    TaxZone::make()
        ->id('for-the-us')
        ->country('US')
        ->save();

    TaxRate::make()
        ->id('used-for-default-address')
        ->name('used for default address')
        ->rate(99)
        ->includeInPrice(true)
        ->category('standard-stuff')
        ->zone('for-the-us')
        ->save();

    $product = Product::make()
        ->price(1000)
        ->taxCategory('standard-stuff')
        ->data([
            'title' => 'Cat Food',
        ]);

    $product->save();

    $order = Order::make()
        ->lineItems([
            [
                'id' => app('stache')->generateId(),
                'product' => $product->id,
                'quantity' => 1,
                'total' => 1000,
            ],
        ]);

    $order->save();

    $recalculate = $order->recalculate();

    // Ensure the tax rate is correct on the line item
    expect(99)->toBe($recalculate->lineItems()->first()->tax()['rate']);

    // Ensure global order tax is right
    expect(497)->toBe($recalculate->get('tax_total'));
});
