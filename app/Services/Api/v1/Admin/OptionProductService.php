<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\OptionProductInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class OptionProductService
{
    public function __construct(
        protected OptionProductInterface $repository
    ) {}

    public function getById(int $id): Model
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function addValues(array $data): Model
    {
        return $this->repository->addValues($data);
    }

    public function getAllValues(int $productId, int $optionId): Collection
    {
        try {
            return $this->repository->getAllValues($productId, $optionId);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }
}
