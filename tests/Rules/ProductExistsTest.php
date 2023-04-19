<?php

use DoubleThreeDigital\SimpleCommerce\Rules\ProductExists;
use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;

uses(TestCase::class);

it('passes if entry exists', function () {
    Collection::make('products')->save();

    $entry = Entry::make()
        ->collection('products');

    $entry->save();

    $validate = Validator::make([
        'entry' => $entry->id(),
    ], [
        'entry' => [new ProductExists()],
    ]);

    expect($validate->fails())->toBeFalse();
});

it('fails if entry does not exist', function () {
    $validate = Validator::make([
        'entry' => 'wippers',
    ], [
        'entry' => [new ProductExists()],
    ]);

    expect($validate->fails())->toBeTrue();
});
