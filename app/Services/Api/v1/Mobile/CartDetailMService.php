<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\CartDetailMInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;

class CartDetailMService
{
    public function __construct(
        protected CartDetailMInterface $repository,
    ) {}

    public function getById(int $cartId, int $branchVariantId): ?Model
    {
        $model = $this->repository->findByBranchVariantId($cartId, $branchVariantId);
        if (!$model) {
            throw new NotFoundException();
        }
        return $model;
    }
}
