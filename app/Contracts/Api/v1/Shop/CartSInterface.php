<?php

namespace App\Contracts\Api\v1\Shop;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Model;

interface CartSInterface
{
    public function getCartByUser(int $userId): ?Model;
    public function getCartBySession(string $sessionId): ?Model;

    /**
     * Obtener o crear carrito por user_id
     */
    public function getOrCreateByUserId(int $userId): Model;

    /**
     * Obtener o crear carrito por session_id
     */
    public function getOrCreateBySessionId(string $sessionId): Model;

    public function update(int $id, array $data): Model;

    /**
     * Cambiar estado carrito a merge
     */
    public function markAsMerged(Cart $model): Model;

    public function markAsCompleted(Model $cart): void;
    public function validateStock(Model $cart): array;
}
