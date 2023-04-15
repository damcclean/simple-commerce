<?php

namespace DoubleThreeDigital\SimpleCommerce\Http\Controllers\CP;

use DoubleThreeDigital\SimpleCommerce\Countries;
use DoubleThreeDigital\SimpleCommerce\Facades\TaxZone;
use DoubleThreeDigital\SimpleCommerce\Http\Requests\CP\TaxZone\CreateRequest;
use DoubleThreeDigital\SimpleCommerce\Http\Requests\CP\TaxZone\DeleteRequest;
use DoubleThreeDigital\SimpleCommerce\Http\Requests\CP\TaxZone\EditRequest;
use DoubleThreeDigital\SimpleCommerce\Http\Requests\CP\TaxZone\IndexRequest;
use DoubleThreeDigital\SimpleCommerce\Http\Requests\CP\TaxZone\StoreRequest;
use DoubleThreeDigital\SimpleCommerce\Http\Requests\CP\TaxZone\UpdateRequest;
use Statamic\Facades\Stache;

class TaxZoneController
{
    public function index(IndexRequest $request)
    {
        return view('simple-commerce::cp.tax-zones.index', [
            'taxZones' => TaxZone::query()->paginate(config('statamic.cp.pagination_size', 50)),
        ]);
    }

    public function create(CreateRequest $request)
    {
        return view('simple-commerce::cp.tax-zones.create', [
            'countries' => Countries::sortBy('name')->all(),
        ]);
    }

    public function store(StoreRequest $request)
    {
        $taxZone = TaxZone::make()
            ->id(Stache::generateId())
            ->name($request->name)
            ->country($request->country);

        if ($request->region) {
            $taxZone->region($request->region);
        }

        $taxZone->save();

        return redirect(cp_route('simple-commerce.tax-zones.index'));
    }

    public function edit(EditRequest $request, $taxZone)
    {
        $taxZone = TaxZone::find($taxZone);

        return view('simple-commerce::cp.tax-zones.edit', [
            'taxZone' => $taxZone,
            'countries' => Countries::sortBy('name')->all(),
        ]);
    }

    public function update(UpdateRequest $request, $taxZone)
    {
        $taxZone = TaxZone::find($taxZone)
            ->name($request->name)
            ->country($request->country);

        if ($request->region) {
            $taxZone->region($request->region);
        }

        $taxZone->save();

        return redirect($taxZone->editUrl());
    }

    public function destroy(DeleteRequest $request, $taxZone)
    {
        TaxZone::find($taxZone)->delete();

        return [
            'success' => true,
        ];
    }
}
