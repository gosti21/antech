<?php

namespace App\Providers;

use App\Contracts\Api\v1\Ia\ProductIaInterface;
use App\Repositories\Api\v1\Ia\ProductIaRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryIaServiceProvider extends ServiceProvider
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
        $this->app->bind(ProductIaInterface::class, ProductIaRepository::class);
    }
}
