<?php

use DuncanMcClean\SimpleCommerce\Http\Controllers\CP\ConvertGuestToUserController;
use DuncanMcClean\SimpleCommerce\Http\Controllers\CP\Coupons\CouponActionController;
use DuncanMcClean\SimpleCommerce\Http\Controllers\CP\Coupons\CouponController;
use DuncanMcClean\SimpleCommerce\Http\Controllers\CP\Orders\OrderActionController;
use DuncanMcClean\SimpleCommerce\Http\Controllers\CP\Orders\OrderController;
use Illuminate\Support\Facades\Route;

Route::name('simple-commerce.')->group(function () {
    Route::resource('coupons', CouponController::class)->except(['destroy']);
    Route::resource('orders', OrderController::class)->only(['index', 'edit', 'update']);

    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::post('actions', [CouponActionController::class, 'run'])->name('actions.run');
        Route::post('actions/list', [CouponActionController::class, 'bulkActions'])->name('actions.bulk');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::post('actions', [OrderActionController::class, 'run'])->name('actions.run');
        Route::post('actions/list', [OrderActionController::class, 'bulkActions'])->name('actions.bulk');
    });

    Route::post('convert-guest-to-user', ConvertGuestToUserController::class)->name('convert-guest-to-user');
});
