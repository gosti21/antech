<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Model;

interface BranchVariantSInterface
{
    public function getById(int $id): Model;
    public function hasStockCart(int $branchVariantId, int $quantity): bool;
}
