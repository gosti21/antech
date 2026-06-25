<?php

namespace App\Services\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\ProductSInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductSService
{
    public  function __construct(
        protected ProductSInterface $repository
    ){}

    public function getAll(int $pagination = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($pagination);
    }

    public function getAllLasts(): Collection
    {
        return $this->repository->getAllLasts();
    }

    public function getAllVariants(int $productId, int $variantId): ?Model
    {
        try {
            return $this->repository->getAllVariants($productId, $variantId);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }
}
