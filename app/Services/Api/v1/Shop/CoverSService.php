<?php

namespace App\Services\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\CoverSInterface;
use Illuminate\Database\Eloquent\Collection;

class CoverSService
{
    public function __construct(
        protected CoverSInterface $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }
}
