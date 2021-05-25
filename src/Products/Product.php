<?php

namespace DoubleThreeDigital\SimpleCommerce\Products;

use DoubleThreeDigital\SimpleCommerce\Contracts\Product as Contract;
use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use DoubleThreeDigital\SimpleCommerce\Support\Traits\HasData;
use DoubleThreeDigital\SimpleCommerce\Support\Traits\IsEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Product implements Contract
{
    use IsEntry;
    use HasData;

    public $id;
    public $site;
    public $title;
    public $slug;
    public $data;
    public $published;

    protected $entry;
    protected $collection;

    public function stockCount()
    {
        if (!isset($this->stock)) {
            return null;
        }

        return (int) $this->data['stock'];
    }

    public function purchasableType(): string
    {
        if (isset($this->data['product_variants']['variants'])) {
            return 'variants';
        }

        return 'product';
    }

    public function variants(): Collection
    {
        if (! isset($this->data['product_variants']['options'])) {
            return collect();
        }

        return collect($this->get('product_variants')['options'])
            ->map(function ($variantOption) {
                return (new ProductVariant)
                    ->key($variantOption['key'])
                    ->name($variantOption['variant'])
                    ->price($variantOption['price'])
                    ->data(Arr::except($variantOption, ['key', 'variant', 'price']));
            });
    }

    public function variant(string $optionKey): ?ProductVariant
    {
        return $this->variants()->filter(function ($variant) use ($optionKey) {
            return $variant->key() === $optionKey;
        })->first();
    }

    public function isExemptFromTax(): bool
    {
        return $this->has('exempt_from_tax')
            && $this->get('exempt_from_tax') === true;
    }

    public function collection(): string
    {
        return SimpleCommerce::productDriver()['collection'];
    }

    public static function bindings(): array
    {
        return [];
    }
}
