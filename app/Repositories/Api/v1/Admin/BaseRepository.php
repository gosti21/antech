<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\BaseInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseInterface
{
    public function __construct(
        protected Model $model
    ){}

    public function getAll(int $pagination): LengthAwarePaginator
    {
        return $this->model->paginate($pagination);
    }

    public function getAllList(): Collection
    {
        return $this->model::all();
    }

    public function getById(int $id): Model
    {
        return $this->model->findOrFail($id);
    }
}
