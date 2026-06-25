<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\BrandInterface;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;

class BrandRepository extends BaseRepository implements BrandInterface
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
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
