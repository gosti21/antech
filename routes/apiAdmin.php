<?php

use App\Http\Controllers\Api\v1\Admin\BranchController;
use App\Http\Controllers\Api\v1\Admin\BranchVariantController;
use App\Http\Controllers\Api\v1\Admin\BrandController;
use App\Http\Controllers\Api\v1\Admin\CategoryController;
use App\Http\Controllers\Api\v1\Admin\CountryController;
use App\Http\Controllers\Api\v1\Admin\CoverController;
use App\Http\Controllers\Api\v1\Admin\CustomerController;
use App\Http\Controllers\Api\v1\Admin\DashboardController;
use App\Http\Controllers\Api\v1\Admin\DepartmentController;
use App\Http\Controllers\Api\v1\Admin\DistrictController;
use App\Http\Controllers\Api\v1\Admin\EmployeeController;
use App\Http\Controllers\Api\v1\Admin\MovementController;
use App\Http\Controllers\Api\v1\Admin\OptionController;
use App\Http\Controllers\Api\v1\Admin\OptionProductController;
use App\Http\Controllers\Api\v1\Admin\OptionValueController;
use App\Http\Controllers\Api\v1\Admin\OrderController;
use App\Http\Controllers\Api\v1\Admin\PaymentMethodController;
use App\Http\Controllers\Api\v1\Admin\ProductController;
use App\Http\Controllers\Api\v1\Admin\ProvinceController;
use App\Http\Controllers\Api\v1\Admin\ReportController;
use App\Http\Controllers\Api\v1\Admin\SaleController;
use App\Http\Controllers\Api\v1\Admin\ShipmentController;
use App\Http\Controllers\Api\v1\Admin\ShippingCompanyController;
use App\Http\Controllers\Api\v1\Admin\SpecificationController;
use App\Http\Controllers\Api\v1\Admin\SubcategoryController;
use App\Http\Controllers\Api\v1\Admin\VariantBarcodeController;
use App\Http\Controllers\Api\v1\Admin\VariantController;
use Illuminate\Support\Facades\Route;

Route::get('customers', [CustomerController::class, 'getAll'])->name('customers.getAll');
Route::get('payment-methods', [PaymentMethodController::class, 'getAllList'])->name('paymentmethods.getAllList');
Route::post('variants/barcodes/generate', [VariantBarcodeController::class, 'generate'])->name('variantBarcodes.generate');
Route::get('couriers/list', [ShippingCompanyController::class, 'getAllList'])->name('couriers.list');
Route::get('categories/list', [CategoryController::class, 'getAllList'])->name('categories.list');
Route::get('subcategories/list', [SubcategoryController::class, 'getAllList'])->name('subcategories.list');
Route::get('brands/list', [BrandController::class, 'getAllList'])->name('brands.list');
Route::get('specifications/list', [SpecificationController::class, 'getAllList'])->name('specifications.list');
Route::get('options/list', [OptionController::class, 'getAllList'])->name('options.list');
Route::get('variants/list', [VariantController::class, 'getAllList'])->name('variants.list');
Route::get('branch-variants/list', [BranchVariantController::class, 'getAllList'])->name('branchVariants.list');
Route::get('countries/list', [CountryController::class, 'getAllList'])->name('countries.list');
Route::post('covers/order', [CoverController::class, 'reorder'])->name('covers.reorder');
Route::get('payment-methods/{id}', [PaymentMethodController::class, 'getById'])->name('paymentmethods.getById');
Route::put('payment-methods/{id}', [PaymentMethodController::class, 'update'])->name('paymentmethods.update');

Route::get('categories/{id}/subcategories', [CategoryController::class, 'getSubcategories'])->name('categories.getSubcategories');
Route::get('countries/{id}/departments', [CountryController::class, 'getDepartments'])->name('categories.getDepartments');
Route::get('departments/{id}/provinces', [DepartmentController::class, 'getProvinces'])->name('categories.getProvinces');
Route::get('options/{id}/values', [OptionController::class, 'getOptionValues'])->name('categories.getOptionValues');

Route::controller(VariantController::class)->prefix('variants')
    ->group(
        function () {
            Route::get('product/{id}/short', 'getAllShort')->name('variants.getAllShort');
        }
    );

Route::apiResources([
    'categories' => CategoryController::class,
    'subcategories' => SubcategoryController::class,
    'brands' => BrandController::class,
    'products' => ProductController::class,
    'covers' => CoverController::class,
    'specifications' => SpecificationController::class,
    'options' => OptionController::class,
    'branches' => BranchController::class,
    'variants' => VariantController::class,
    'countries' => CountryController::class,
    'departments' => DepartmentController::class,
    'provinces' => ProvinceController::class,
    'districts' => DistrictController::class,
    'employees' => EmployeeController::class,
    'couriers' => ShippingCompanyController::class,
]);

Route::controller(SaleController::class)->prefix('sales')
    ->group(
        function () {
            Route::get('/', 'index')->name('sales.index');
            Route::get('/{id}', 'show')->name('sales.show');
        }
    );

Route::controller(OrderController::class)->prefix('orders')
    ->group(
        function () {
            Route::get('/', 'index')->name('orders.index');
            Route::get('/{id}/pdf', 'getPdf')->name('orders.getPdf');
            Route::patch('/{id}', 'update')->name('orders.update');
        }
    );

Route::controller(ShipmentController::class)->prefix('shipments')
    ->group(
        function () {
            Route::get('/', 'index')->name('shipments.index');
            Route::patch('/{id}', 'update')->name('shipments.update');
        }
    );

Route::controller(MovementController::class)->prefix('movements')
    ->group(
        function () {
            Route::get('/', 'index')->name('movements.index');
            Route::post('/', 'store')->name('movements.store');
            Route::get('/{id}', 'show')->name('movements.show');
        }
    );

Route::controller(ProductController::class)->prefix('products')
    ->group(
        function () {
            Route::get('/{id}/options', 'getAllOptions')->name('optionValues.getAllOptions');
            Route::get('/{id}/hasOptions', 'hasOptions')->name('optionValues.hasOptions');
            Route::get('/{id}/optionsList', 'getAllOptionsShort')->name('optionValues.getAllOptionsShort');
        }
    );

Route::controller(OptionValueController::class)->prefix('option-values')
    ->group(
        function () {
            Route::post('/', 'store')->name('optionValues.store');
            Route::get('/{id}', 'show')->name('optionValues.show');
        }
    );

Route::controller(OptionProductController::class)->prefix('option-products')
    ->group(
        function () {
            Route::post('/', 'store')->name('optionProducts.store');
            Route::post('/values', 'addValues')->name('optionProducts.addValues');
            Route::get('/{id}', 'show')->name('optionProducts.show');
            Route::get('/{productId}/values/{optionId}', 'getAllValues')->name('optionProducts.getAllValues');
        }
    );

Route::controller(DashboardController::class)->prefix('dashboard')
    ->group(
        function () {
            Route::get('/stats', 'getStats')->name('dashboard.getStats');
            Route::get('/sales-chart', 'getSalesChart')->name('dashboard.getSalesChart');
            Route::get('/top-variants', 'getTopVariants')->name('dashboard.getTopVariants');
            Route::get('/top-categories', 'getTopCategories')->name('dashboard.getTopCategories');
            Route::get('/top-brands', 'getTopBrands')->name('dashboard.getTopBrands');
        }
    );

Route::controller(ReportController::class)->prefix('reports')
    ->group(
        function () {
            Route::post('/low-stock', 'lowStock')->name('reports.lowStock');
            Route::post('/sales', 'sales')->name('reports.sales');
        }
    );
