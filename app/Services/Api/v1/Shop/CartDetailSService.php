<?php

namespace App\Services\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\CartDetailSInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;

class CartDetailSService
{
    public function __construct(
        protected CartDetailSInterface $repository,
    ) {}

    public function getById(int $cartId, int $branchVariantId): ?Model
    {
        $model = $this->repository->findByBranchVariantId($cartId, $branchVariantId);
        if(!$model) {
            throw new NotFoundException();
        }
        return $model;
    }
}
