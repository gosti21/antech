<?php

use App\Http\Controllers\Api\v1\Mobile\BrandMController;
use App\Http\Controllers\Api\v1\Mobile\CartMController;
use App\Http\Controllers\Api\v1\Mobile\CategoryMController;
use App\Http\Controllers\Api\v1\Mobile\CustomerMController;
use App\Http\Controllers\Api\v1\Mobile\MethodPaymentMController;
use App\Http\Controllers\Api\v1\Mobile\OrderMController;
use App\Http\Controllers\Api\v1\Mobile\ProductMController;
use App\Http\Controllers\Api\v1\Mobile\VariantMController;
use App\Http\Controllers\Api\v1\Mobile\VoucherMController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CategoryMController::class, 'getAllList'])->name('categories.list');
Route::post('orders', [OrderMController::class, 'store'])->name('orders.store');
Route::get('orders', [OrderMController::class, 'getAll'])->name('orders.getAll');
Route::get('brands', [BrandMController::class, 'getAllList'])->name('brands.list');
Route::get('orders/{id}', [OrderMController::class, 'show'])->name('vouchers.show');
Route::get('vouchers/{id}', [VoucherMController::class, 'show'])->name('orders.show');
Route::get('method-payments/yape', [MethodPaymentMController::class, 'getYape'])->name('methodPayments.getYape');
Route::get('method-payments/plin', [MethodPaymentMController::class, 'getPlin'])->name('methodPayments.getPlin');
Route::get('variants/{sku}', [VariantMController::class, 'getVariantSku'])->name('variants.getVariantSku');
Route::get('categories/{id}/subcategories', [CategoryMController::class, 'getSubcategories'])->name('categories.getSubcategories');
Route::get('customers/dni/{dni}', [CustomerMController::class, 'searchDNI'])->name('customers.searchDNI');
Route::get('customers/ruc/{ruc}', [CustomerMController::class, 'searchRUC'])->name('customers.searchRUC');

Route::controller(CartMController::class)->prefix('cart')
    ->group(
        function () {
            Route::get('/', 'getCart')->name('cart.getCart');
            Route::post('/addItem', 'addItem')->name('cart.addItem');
            Route::put('/updateItem/{id}', 'updateItem')->name('cart.updateItem');
            Route::delete('/removeItem/{id}', 'removeItem')->name('cart.removeItem');
            Route::delete('/delete', 'deleteCart')->name('cart.deleteCart');
        }
    );

Route::controller(ProductMController::class)->prefix('products')
    ->group(
        function () {
            Route::get('/', 'getAll')->name('products.getAll');
            Route::get('/{productId}/variants/{variantId}', 'getAllVariants')->name('products.getAllVariants');
        }
    );
