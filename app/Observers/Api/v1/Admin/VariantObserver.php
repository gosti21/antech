<?php

namespace App\Observers\Api\v1\Admin;

use App\Jobs\SyncProductCatalogToIa;
use App\Models\Variant;

class VariantObserver
{
    /**
     * Handle the Variant "created" event.
     */
    public function created(Variant $variant): void
    {
        $this->syncCatalog();
    }

    /**
     * Handle the Variant "updated" event.
     */
    public function updated(Variant $variant): void
    {
        // Solo si cambió precio (las características son importantes)
        if ($variant->wasChanged(['selling_price', 'status'])) {
            $this->syncCatalog();
        }
    }

    /**
     * Handle the Variant "deleted" event.
     */
    public function deleted(Variant $variant): void
    {
        $this->syncCatalog();
    }

    /**
     * Handle the Variant "restored" event.
     */
    public function restored(Variant $variant): void
    {
        //
    }

    /**
     * Handle the Variant "force deleted" event.
     */
    public function forceDeleted(Variant $variant): void
    {
        //
    }

    private function syncCatalog(): void
    {
        SyncProductCatalogToIa::dispatch()->delay(now()->addSeconds(5));
    }
}
