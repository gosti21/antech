<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\ProductMInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductMService
{
    public function __construct(
        protected ProductMInterface $repository
    ){}

    public function getAll(int $pagination = 16): LengthAwarePaginator
    {
        return $this->repository->getAll($pagination);
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
