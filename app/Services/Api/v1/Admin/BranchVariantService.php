<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\BranchVariantInterface;
use Illuminate\Database\Eloquent\Collection;

class BranchVariantService
{
    public function __construct(
        protected BranchVariantInterface $repository
    ) {}

    public function getAllList(): Collection
    {
        return $this->repository->getAllList();
    }
}
