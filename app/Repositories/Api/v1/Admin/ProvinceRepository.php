<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\ProvinceInterface;
use App\Models\Province;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ProvinceRepository extends BaseRepository implements ProvinceInterface
{
    public function __construct(Province $model)
    {
        parent::__construct($model);
    }

    public function getAll(int $pagination): LengthAwarePaginator
    {
        return $this->model::with('department')->paginate($pagination);
    }

    public function getById(int $id): Model
    {
        return $this->model::with('department')->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data)->refresh()->load('department');
    }

    public function update(array $data, int $id): Model
    {
        $model = $this->getById($id);
        $model->update($data);
        return $model->refresh()->load('department');
    }
}
