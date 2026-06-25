<?php

namespace App\Observers\Api\v1\Admin;

use App\Models\Cover;

class CoverObserver
{
    /**
     * Handle the Cover "created" event.
     */
    public function created(Cover $cover): void
    {
        //
    }

    public function creating(Cover $cover): void
    {
        $cover->order = Cover::max('order') + 1;
    }

    /**
     * Handle the Cover "updated" event.
     */
    public function updated(Cover $cover): void
    {
        //
    }

    /**
     * Handle the Cover "deleted" event.
     */
    public function deleted(Cover $cover): void
    {
        //
    }

    /**
     * Handle the Cover "restored" event.
     */
    public function restored(Cover $cover): void
    {
        //
    }

    /**
     * Handle the Cover "force deleted" event.
     */
    public function forceDeleted(Cover $cover): void
    {
        //
    }
}
