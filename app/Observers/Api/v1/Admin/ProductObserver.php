<?php

namespace App\Observers\Api\v1\Admin;

use App\Jobs\SyncProductCatalogToIa;
use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->syncCatalog();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Solo sincronizar si cambió algo relevante para la IA
        if ($product->wasChanged(['name', 'model', 'description', 'status'])) {
            $this->syncCatalog();
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->syncCatalog();
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }

    /**
     * Encola job de sincronización
     */
    private function syncCatalog(): void
    {
        // Dispatch con delay de 5 segundos
        // (por si hacen múltiples cambios seguidos, solo sincroniza 1 vez)
        SyncProductCatalogToIa::dispatch()->delay(now()->addSeconds(5));
    }
}
