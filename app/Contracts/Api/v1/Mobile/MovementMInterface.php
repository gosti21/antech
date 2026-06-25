<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Database\Eloquent\Model;

interface MovementMInterface
{
    public function create(array $movementData): Model;
    public function attachInventoryMovement(Model $movement, int $branchVariantId, int $quantity): void;
    public function decrementStock(int $branchVariantId, int $quantity): Model;
    public function verifyStockWithLock(int $branchVariantId, int $requiredQuantity): bool;
}
