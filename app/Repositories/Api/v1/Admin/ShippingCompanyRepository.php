<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\ShippingCompanyInterface;
use App\Models\ShippingCompany;
use Illuminate\Database\Eloquent\Model;

class ShippingCompanyRepository extends BaseRepository implements ShippingCompanyInterface
{
    public function __construct(ShippingCompany $model)
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
