<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\MovementSInterface;
use App\Models\BranchVariant;
use App\Models\Movement;
use Illuminate\Database\Eloquent\Model;

class MovementSRepository implements MovementSInterface
{
    /**
     * Crear un movimiento de inventario
     */
    public function create(array $movementData): Movement
    {
        return Movement::create($movementData);
    }

    /**
     * Registrar movimiento de inventario (tabla intermedia)
     */
    public function attachInventoryMovement(Model $movement, int $branchVariantId, int $quantity): void
    {
        $movement->branchVariants()->attach($branchVariantId, [
            'quantity' => $quantity
        ]);
    }

    /**
     * Descontar stock con lock pesimista
     */
    public function decrementStock(int $branchVariantId, int $quantity): Model
    {
        $branchVariant = BranchVariant::lockForUpdate()->findOrFail($branchVariantId);
        $branchVariant->decrement('stock', $quantity);

        return $branchVariant;
    }

    /**
     * Verificar stock disponible con lock
     */
    public function verifyStockWithLock(int $branchVariantId, int $requiredQuantity): bool
    {
        $branchVariant = BranchVariant::lockForUpdate()->findOrFail($branchVariantId);

        return $branchVariant->stock >= $requiredQuantity;
    }
}
