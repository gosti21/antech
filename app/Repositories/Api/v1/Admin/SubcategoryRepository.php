<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\SubcategoryInterface;
use App\Models\Subcategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SubcategoryRepository extends BaseRepository implements SubcategoryInterface
{
    public function __construct(Subcategory $model)
    {
        parent::__construct($model);
    }

    public function getAll(int $pagination): LengthAwarePaginator
    {
        return $this->model::with('category')->paginate($pagination);
    }

    public function getAllList(): Collection
    {
        return $this->model::with('category')->get();
    }

    public function getById(int $id): Model
    {
        return $this->model::with('category')->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data)->refresh()->load('category');
    }

    public function update(array $data, int $id): Model
    {
        $model = $this->getById($id);
        $model->update($data);
        return $model->refresh()->load('category');
    }
}
