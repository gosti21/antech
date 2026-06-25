<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Model;

interface CartDetailSInterface
{
    /**
     * Buscar item en el carrito por branch_variant_id
     */
    public function findByBranchVariantId(int $cartId, int $branchVariantId): ?Model;

    /**
     * Crear item en el carrito
     */
    public function create(int $cartId, array $data): Model;

    /**
     * Actualizar cantidad de un item
     */
    public function update(int $cartDetailId, int $data): Model;

    /**
     * Eliminar item del carrito
     */
    public function deleteItem(int $cartId, int $branchVariantId): bool;

    /**
     * Eliminar todos los items del carrito
     */
    public function deleteAll(int $cartId): int;

    /**
     * Transferir item a otro carrito
     */
    public function transferToCart(int $cartDetailId, int $newCartId): bool;

    /**
     * Eliminar un detalle específico por ID
     */
    public function deleteById(int $cartDetailId): bool;
}
