<?php

use App\Http\Controllers\Api\v1\Shop\AddressSController;
use App\Http\Controllers\Api\v1\Shop\BranchSController;
use App\Http\Controllers\Api\v1\Shop\CartSController;
use App\Http\Controllers\Api\v1\Shop\CategorySController;
use App\Http\Controllers\Api\v1\Shop\CoverSController;
use App\Http\Controllers\Api\v1\Shop\CustomerSController;
use App\Http\Controllers\Api\v1\Shop\LocationSController;
use App\Http\Controllers\Api\v1\Shop\OrderSController;
use App\Http\Controllers\Api\v1\Shop\ProductSController;
use App\Http\Middleware\Api\v1\OptionalSanctumAuth;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CategorySController::class, 'getAll'])->name('categories.getAll');
Route::get('categories/{id}', [CategorySController::class, 'show'])->name('categories.show');
Route::get('covers', [CoverSController::class, 'getAll'])->name('covers.getAll');

Route::controller(CartSController::class)->prefix('cart')->middleware(OptionalSanctumAuth::class)
    ->group(
        function () {
            Route::get('/', 'getCart')->name('cart.getCart');
            Route::post('/addItem', 'addItem')->name('cart.addItem');
            Route::put('/updateItem/{id}', 'updateItem')->name('cart.updateItem');
            Route::delete('/removeItem/{id}', 'removeItem')->name('cart.removeItem');
            Route::delete('/delete', 'deleteCart')->name('cart.deleteCart');
        }
    );

Route::controller(ProductSController::class)->prefix('products')
    ->group(
        function () {
            Route::get('/', 'getAll')->name('products.getAll');
            Route::get('/last', 'getAllLasts')->name('products.getAllLasts');
            Route::get('/{productId}/variants/{variantId}', 'getAllVariants')->name('products.getAllVariants');
        }
    );

Route::middleware('auth:sanctum')->group(
    function () {
        Route::post('cart/merge', [CartSController::class, 'merge'])->name('cart.merge');
        Route::get('branches', [BranchSController::class, 'getAll'])->name('branches.getAll');
        Route::post('orders', [OrderSController::class, 'orderCreate'])->name('orders.orderCreate');
        Route::get('checkout/address/favorite', [AddressSController::class, 'favorite'])->name('address.favorite');
        Route::apiResource('checkout/address', AddressSController::class);
        Route::get('locations/departments', [LocationSController::class, 'getAllDepartments'])->name('locations.getAllDepartments');
        Route::get('locations/{id}/provinces', [LocationSController::class, 'getProvinces'])->name('locations.getProvinces');
        Route::get('locations/{id}/districts', [LocationSController::class, 'getDistricts'])->name('locations.getDistricts');
        Route::get('customers/dni/{dni}', [CustomerSController::class, 'searchDNI'])->name('customers.searchDNI');
        Route::get('customers/ruc/{ruc}', [CustomerSController::class, 'searchRUC'])->name('customers.searchRUC');
    }
);

Route::post('orders/confirm', [OrderSController::class, 'confirmOrder'])->name('orders.confirmOrder');
