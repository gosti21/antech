<?php

namespace App\Observers\Api\v1\Admin;

use App\Models\Address;

class AddressSObserver
{
    /**
     * Handle the Address "created" event.
     */
    public function created(Address $address): void
    {
        //
    }

    public function creating(Address $address): void
    {
        $exists = Address::where('addressable_type', $address->addressable_type)
            ->where('addressable_id', $address->addressable_id)
            ->exists();

        if (! $exists) {
            $address->favorite = true;
        }
    }

    /**
     * Handle the Address "updated" event.
     */
    public function updated(Address $address): void
    {
        //
    }

    public function updating(Address $address): void
    {
        // 1️⃣ Si el request NO incluye favorite → no hacer nada
        if (! $address->isDirty('favorite')) {
            return;
        }

        // Datos del dueño
        $type = $address->addressable_type;
        $id   = $address->addressable_id;

        /**
         * 2️⃣ Si favorite viene en TRUE
         * → todas las demás direcciones pasan a false
         */
        if ($address->favorite === true) {
            Address::where('addressable_type', $type)
                ->where('addressable_id', $id)
                ->where('id', '!=', $address->id)
                ->update(['favorite' => false]);

            return;
        }

        /**
         * 3️⃣ Si favorite viene en FALSE
         * → verificar que exista al menos una en true (excluyendo esta)
         * → si no existe, cancelar el cambio
         */
        $existsAnotherFavorite = Address::where('addressable_type', $type)
            ->where('addressable_id', $id)
            ->where('id', '!=', $address->id)
            ->where('favorite', true)
            ->exists();

        if (! $existsAnotherFavorite) {
            // Cancelar el cambio
            $address->favorite = true;
        }
    }

    /**
     * Handle the Address "deleted" event.
     */
    public function deleted(Address $address): void
    {
        //
    }

    public function deleting(Address $address): void
    {
        // Si no era favorita, no hacemos nada
        if (! $address->favorite) {
            return;
        }

        // Desmarcar esta antes del delete (clave para el índice)
        $address->favorite = false;
        $address->saveQuietly();

        // Buscar otra dirección NO eliminada
        $nextFavorite = Address::where('addressable_type', $address->addressable_type)
            ->where('addressable_id', $address->addressable_id)
            ->where('id', '!=', $address->id)
            ->first();

        if ($nextFavorite) {
            $nextFavorite->updateQuietly(['favorite' => true]);
        }
    }

    /**
     * Handle the Address "restored" event.
     */
    public function restored(Address $address): void
    {
        //
    }

    /**
     * Handle the Address "force deleted" event.
     */
    public function forceDeleted(Address $address): void
    {
        //
    }
}
