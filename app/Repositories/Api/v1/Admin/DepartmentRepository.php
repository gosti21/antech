<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\DepartmentInterface;
use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DepartmentRepository extends BaseRepository implements DepartmentInterface
{
    public function __construct(Department $model)
    {
        parent::__construct($model);
    }

    public function getAll(int $pagination): LengthAwarePaginator
    {
        return $this->model::with('country')->paginate($pagination);
    }

    public function getById(int $id): Model
    {
        return $this->model::with('country')->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data)->refresh()->load('country');
    }

    public function update(array $data, int $id): Model
    {
        $model = $this->getById($id);
        $model->update($data);
        return $model->refresh()->load('country');
    }

    public function getProvinces(int $id): Collection
    {
        $model = $this->getById($id);
        return $model->provinces()->get(['id', 'name']);
    }
}
