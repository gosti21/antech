<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\CategoryMInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryMService
{
    public function __construct(
        protected CategoryMInterface $repository
    ){ }

    public function getAllList(): Collection
    {
        return $this->repository->getAllList();
    }

    public function getSubcategories(int $id): Collection
    {
        try {
            return $this->repository->getSubcategories($id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }
}
