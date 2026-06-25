<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Database\Eloquent\Model;

interface BranchVariantMInterface
{
    public function getById(int $id): Model;
    public function hasStockCart(int $branchVariantId, int $quantity): bool;
}
