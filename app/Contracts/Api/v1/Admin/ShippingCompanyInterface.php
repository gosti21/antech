<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Model;

interface ShippingCompanyInterface extends BaseInterface
{
    public function create(array $data): Model;
    public function update(array $data, int $id): Model;
}
