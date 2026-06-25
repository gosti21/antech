<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Model;

interface DistrictInterface extends BaseInterface
{
    public function create(array $districData, array $shippingRateData): Model;
    public function update(array $districData, array $shippingRateData, int $id): Model;
}
