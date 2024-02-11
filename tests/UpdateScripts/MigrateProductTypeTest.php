<?php

use DoubleThreeDigital\SimpleCommerce\UpdateScripts\v6_0\MigrateProductType;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;

it('updates product_type field for physical product', function () {
    $productEntry = Entry::make()
        ->collection('products')
        ->id('test')
        ->data(['price' => 1234]);

    $productEntry->save();

    (new MigrateProductType('doublethreedigital/simple-commerce', '6.0.0'))->update();

    $productEntry->fresh();

    expect($productEntry->get('product_type'))->toBe('physical');
});

it('updates product_type field for digital product', function () {
    $productEntry = Entry::make()
        ->collection('products')
        ->id('test')
        ->data(['is_digital_product' => true, 'price' => 1234]);

    $productEntry->save();

    (new MigrateProductType('doublethreedigital/simple-commerce', '6.0.0'))->update();

    $productEntry->fresh();

    expect($productEntry->get('product_type'))->toBe('digital');
});

it('updates product_type field for digital product with variants', function () {
    $productEntry = Entry::make()
        ->collection('products')
        ->id('test')
        ->data([
            'product_variants' => [
                'variants' => [
                    ['name' => 'Colours', 'values' => ['Red']],
                    ['name' => 'Sizes', 'values' => ['Small']],
                ],
                'options' => [
                    ['key' => 'Red_Small', 'variant' => 'Red Small', 'price' => 1200, 'is_digital_product' => true],
                ],
            ],
        ]);

    $productEntry->save();

    (new MigrateProductType('doublethreedigital/simple-commerce', '6.0.0'))->update();

    $productEntry->fresh();

    expect($productEntry->get('product_type'))->toBe('digital');
});

it('adds product type field and removes old digital product fields from product blueprints', function () {
    $collection = Collection::find('products');

    $blueprint = $collection->entryBlueprint()->setContents([
        'fields' => [
            ['handle' => 'is_digital_product', 'field' => ['type' => 'toggle']],
            ['handle' => 'downloadable_asset', 'field' => ['type' => 'assets']],
            ['handle' => 'download_limit', 'field' => ['type' => 'integer']],
        ],
    ]);

    (new MigrateProductType('doublethreedigital/simple-commerce', '6.0.0'))->update();

    $blueprint = $collection->entryBlueprint();

    expect($blueprint->fields()->all()->map->handle()->toArray())
        ->toHaveKey('product_type')
        ->not->toHaveKey('is_digital_product')
        ->toHaveKey('downloadable_asset')
        ->toHaveKey('download_limit');
});

it('adds product type field and removes old digital product fields from variant options field from product blueprints', function () {
    $collection = Collection::find('products');

    $blueprint = $collection->entryBlueprint()->setContents([
        'fields' => [
            [
                'handle' => 'product_variants',
                'field' => [
                    'type' => 'product_variants',
                    'option_fields' => [
                        ['handle' => 'is_digital_product', 'field' => ['type' => 'toggle']],
                        ['handle' => 'downloadable_asset', 'field' => ['type' => 'assets']],
                        ['handle' => 'download_limit', 'field' => ['type' => 'integer']],
                    ],
                ],
            ],
        ],
    ]);

    (new MigrateProductType('doublethreedigital/simple-commerce', '6.0.0'))->update();

    $blueprint = $collection->entryBlueprint();

    expect($blueprint->fields()->all()->map->handle()->toArray())
        ->toHaveKey('product_type')
        ->not->toHaveKey('is_digital_product')
        ->not->toHaveKey('downloadable_asset')
        ->not->toHaveKey('download_limit')
        ->toHaveKey('product_variants');

    expect($blueprint->field('product_variants')->config()['option_fields'])
        ->not->toHaveKey('is_digital_product')
        ->not->toHaveKey('downloadable_asset')
        ->not->toHaveKey('download_limit');
});
