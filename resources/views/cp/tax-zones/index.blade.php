@extends('statamic::layout')
@section('title', __('Tax Zones'))
@section('wrapper_class', 'max-w-full')

@section('content')
    <div class="flex items-center justify-between mb-3">
        <h1 class="flex-1">{{ __('Tax Zones') }}</h1>

        @if(auth()->user()->can('create tax zones'))
            <a class="btn-primary" href="{{ cp_route('simple-commerce.tax-zones.create') }}">{{ __('Create Tax Zone') }}</a>
        @endif
    </div>

    @if ($taxZones->count())
        <div class="card p-0">
            @include('simple-commerce::cp.partials.tax-navigation')

            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Location') }}</th>
                        <th class="actions-column"></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($taxZones as $taxZone)
                        <tr id="taxZone_{{ $taxZone->id() }}">
                            <td>
                                <div class="flex items-center">
                                    <a href="{{ $taxZone->editUrl() }}">{{ $taxZone->name() }}</a>
                                </div>
                            </td>
                            <td>
                                @if($taxZone->country())
                                    @if($taxZone->region()){{ $taxZone->region()['name'] }}, @endif{{ $taxZone->country()['name'] }}
                                @else
                                    {{ __('Everywhere') }}
                                @endif
                            </td>
                            <td class="flex justify-end">
                                <dropdown-list class="mr-1">
                                    @if(auth()->user()->can('edit tax zones'))
                                        <dropdown-item :text="__('Edit')" redirect="{{ $taxZone->editUrl() }}"></dropdown-item>
                                    @endif

                                    @if($taxZone->id() !== 'everywhere' && auth()->user()->can('delete tax zones'))
                                        <dropdown-item :text="__('Delete')" class="warning" @click="$refs.deleter.confirm()">
                                            <resource-deleter
                                                ref="deleter"
                                                resource-title="{{ $taxZone->name() }}"
                                                route="{{ $taxZone->deleteUrl() }}"
                                                :reload="true"
                                                @deleted="document.getElementById('taxZone_{{ $taxZone->id() }}').remove()"
                                            ></resource-deleter>
                                        </dropdown-item>
                                    @endif
                                </dropdown-list>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="my-2">
            {{ $taxZones->links('simple-commerce::cp.partials.pagination') }}
        </div>
    @else
        @include('statamic::partials.create-first', [
            'resource' => __('Tax Zone'),
            'svg' => 'empty/collection',
            'route' => cp_route('simple-commerce.tax-zones.create'),
        ])
    @endif
@endsection
