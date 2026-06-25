<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\CountryInterface;
use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CountryRepository extends BaseRepository implements CountryInterface
{
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }

    public function getDepartments(int $id): Collection
    {
        $model = $this->getById($id);
        return $model->departaments()->get(['id', 'name']);
    }

    public function getAllList(): Collection
    {
        return $this->model::get(['id', 'name']);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data)->refresh();
    }

    public function update(array $data, int $id): Model
    {
        $model = $this->getById($id);
        $model->update($data);
        return $model->refresh();
    }
}
