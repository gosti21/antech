<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\ShipmentInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShipmentService
{
    public function __construct(
        protected ShipmentInterface $repository
    ) {}

    public function getAll(int $pagination = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($pagination);
    }

    public function update(array $data, $id)
    {
        try {
            return $this->repository->update($data, $id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }
}
