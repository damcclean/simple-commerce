<?php

namespace Damcclean\Commerce\Http\Controllers\Cp;

use Damcclean\Commerce\Facades\Coupon;
use Damcclean\Commerce\Policies\CouponPolicy;
use Statamic\Http\Controllers\CP\CpController;

class CouponSearchController extends CpController
{
    public function __invoke()
    {
        $this->authorize('view', CouponPolicy::class);

        $query = request()->input('search');

        if (! $query) {
            $results = Coupon::all();
        } else {
            $results = Coupon::all()
                ->filter(function ($item) use ($query) {
                    return false !== stristr((string) $item['title'], $query);
                });
        }

        return response()->json([
            'data' => $results,
            'links' => [],
            'meta' => [
                'path' => cp_route('coupons.search'),
                'sortColumn' => 'title',
            ]
        ]);
    }
}
