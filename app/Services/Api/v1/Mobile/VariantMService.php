<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\VariantMInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VariantMService
{
    public function __construct(
        protected VariantMInterface $repository
    ) {}

    public function getVariantSku(string $sku): ?Model
    {
        try {
            return $this->repository->getVariantSku($sku);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }
}
