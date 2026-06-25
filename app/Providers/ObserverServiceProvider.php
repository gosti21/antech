<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Cover;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
use App\Observers\Api\v1\Admin\AddressSObserver;
use App\Observers\Api\v1\Admin\CoverObserver;
use App\Observers\Api\v1\Admin\ProductObserver;
use App\Observers\Api\v1\Admin\VariantObserver;
use App\Observers\Api\v1\OrderObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Cover::observe(CoverObserver::class);
        Address::observe(AddressSObserver::class);
        Order::observe(OrderObserver::class);
        //Activarlos luego al final
        /* Product::observe(ProductObserver::class); */
        /* Variant::observe(VariantObserver::class); */
    }
}
