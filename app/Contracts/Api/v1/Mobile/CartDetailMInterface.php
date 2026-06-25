<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Database\Eloquent\Model;

interface CartDetailMInterface {
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
     * Eliminar un detalle específico por ID
     */
    public function deleteById(int $cartDetailId): bool;
}
