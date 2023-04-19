<?php

use DoubleThreeDigital\SimpleCommerce\Fieldtypes\ShippingMethodFieldtype;
use DoubleThreeDigital\SimpleCommerce\Shipping\FreeShipping;
use DoubleThreeDigital\SimpleCommerce\Tests\Helpers\Invader;
use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Statamic\CP\Column;

uses(TestCase::class);
beforeEach(function () {
    $this->fieldtype = new ShippingMethodFieldtype;
});


test('can get config field items', function () {
    $configFieldItems = (new Invader($this->fieldtype))->configFieldItems();

    expect($configFieldItems)->toBeArray();
});

test('can get index items', function () {
    $getIndexItems = $this->fieldtype->getIndexItems(new Request());

    expect($getIndexItems instanceof Collection)->toBeTrue();

    $this->assertSame($getIndexItems->last(), [
        'id' => FreeShipping::class,
        'name' => 'Free Shipping',
        'title' => 'Free Shipping',
    ]);
});

test('can get columns', function () {
    $getColumns = (new Invader($this->fieldtype))->getColumns();

    expect($getColumns)->toBeArray();

    expect($getColumns[0] instanceof Column)->toBeTrue();
    expect('name')->toBe($getColumns[0]->field());
    expect('Name')->toBe($getColumns[0]->label());
});

test('can return as item array', function () {
    $toItemArray = $this->fieldtype->toItemArray(FreeShipping::class);

    expect($toItemArray)->toBeArray();

    $this->assertSame($toItemArray, [
        'id' => FreeShipping::class,
        'title' => 'Free Shipping',
    ]);
});

test('can preprocess index', function () {
    $preProcessIndex = $this->fieldtype->preProcessIndex(FreeShipping::class);

    expect($preProcessIndex)->toBeString();
    expect('Free Shipping')->toBe($preProcessIndex);
});

test('can preprocess index with no shipping method', function () {
    $preProcessIndex = $this->fieldtype->preProcessIndex(null);

    expect($preProcessIndex)->toBeNull();
});
