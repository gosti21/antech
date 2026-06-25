<?php

namespace App\Contracts\Api\v1\Shop;

use Illuminate\Database\Eloquent\Model;

interface ShipmentSInterface
{
    public function create(array $dataShipment): Model;
}
