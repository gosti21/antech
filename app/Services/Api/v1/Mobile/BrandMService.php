<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\BrandMInterface;
use Illuminate\Database\Eloquent\Collection;

clasS BrandMService
{
    public function __construct(
        protected BrandMInterface $repository
    ) {}

    public function getAllList(): Collection
    {
        return $this->repository->getAllList();
    }
}
