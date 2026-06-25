<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\BranchVariantSInterface;
use App\Models\BranchVariant;
use Illuminate\Database\Eloquent\Model;

class BranchVariantSRepository implements BranchVariantSInterface
{
    public function getById(int $id): Model
    {
        return BranchVariant::findOrFail($id);
    }

    /**
     * Verificar stock disponible
     */
    public function hasStockCart(int $branchVariantId, int $quantity): bool
    {
        $branchVariant = $this->getById($branchVariantId);
        return $branchVariant->stock >= $quantity;
    }
}
