<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ProductSInterface
{
    public function getAll(int $pagination): LengthAwarePaginator;
    public function getAllLasts(): Collection;
    public function getAllVariants(int $productId, int $variantId): Model;
}
