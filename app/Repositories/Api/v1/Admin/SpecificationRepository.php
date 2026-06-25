<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\SpecificationInterface;
use App\Models\Specification;
use Illuminate\Database\Eloquent\Model;

class SpecificationRepository extends BaseRepository implements SpecificationInterface
{
    public function __construct(Specification $model)
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
