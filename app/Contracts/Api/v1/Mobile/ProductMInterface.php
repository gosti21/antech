<?php

namespace App\Contracts\Api\v1\Mobile;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface ProductMInterface
{
    public function getAll(int $pagination): LengthAwarePaginator;
    public function getAllVariants(int $productId, int $variantId): Model;
}
