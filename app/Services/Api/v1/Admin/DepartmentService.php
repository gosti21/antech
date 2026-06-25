<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\DepartmentInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @extends BaseService<DepartmentInterface>
 */
class DepartmentService extends BaseService
{
    public function __construct(DepartmentInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(array $data, int $id): Model
    {
        try {
            return $this->repository->update($data, $id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }

    public function getProvinces(int $id): Collection
    {
        try {
            return $this->repository->getProvinces($id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }
}
