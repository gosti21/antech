<?php

namespace App\Contracts\Api\v1\Ia;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductIaInterface
{
    public function getAllForAI(): Collection;
    public function getByIdForAI(int $productId): ?Product;
}
