<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\SaleInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SaleService
{
    public function __construct(
        protected SaleInterface $repository
    ) {}

    public function getAll(int $pagination = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($pagination);
    }

    public function getById(int $id): ?Model
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }
}
