<?php

namespace DuncanMcClean\SimpleCommerce\Taxes;

use DuncanMcClean\SimpleCommerce\Facades;
use DuncanMcClean\SimpleCommerce\Contracts\Taxes\TaxZone as Contract;
use Illuminate\Support\Collection;
use Statamic\Contracts\Data\Augmentable;
use Statamic\Data\ContainsData;
use Statamic\Data\HasAugmentedData;
use Statamic\Support\Traits\FluentlyGetsAndSets;

class TaxZone implements Contract, Augmentable
{
    use FluentlyGetsAndSets, ContainsData, HasAugmentedData;

    public $handle;

    public function __construct()
    {
        $this->data = collect();
    }

    public function handle($handle = null)
    {
        return $this->fluentlyGetOrSet('handle')->args(func_get_args());
    }

    public function rates(): Collection
    {
        return collect($this->get('rates'))->reject(fn ($rate) => is_null($rate));
    }

    public function save(): bool
    {
        return Facades\TaxZone::save($this);
    }

    public function delete(): bool
    {
        return Facades\TaxZone::delete($this->handle());
    }

    public function editUrl()
    {
        return cp_route('simple-commerce.tax-zones.edit', $this->handle());
    }

    public function updateUrl()
    {
        return cp_route('simple-commerce.tax-zones.update', $this->handle());
    }

    public function deleteUrl()
    {
        return cp_route('simple-commerce.tax-zones.destroy', $this->handle());
    }

    public function toArray(): array
    {
        return $this->data()->merge([
            'handle' => $this->handle(),
        ])->all();
    }

    public function fileData()
    {
        return $this->data()->merge([
            'rates' => $this->rates()->all(),
        ])->filter()->all();
    }

    public function augmentedArrayData()
    {
        return $this->toArray();
    }
}