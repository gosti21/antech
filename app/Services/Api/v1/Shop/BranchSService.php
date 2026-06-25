<?php

namespace App\Services\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\BranchSInterface;
use Illuminate\Database\Eloquent\Collection;

class BranchSService
{
    public function __construct(
        protected BranchSInterface $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }
}
