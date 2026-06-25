<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\ShipmentSInterface;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class ShipmentSRepository implements ShipmentSInterface
{
    public function create(array $dataShipment): Model
    {
        return Shipment::create([
            'receiver_info' => $dataShipment['receiver_info'],
            'delivery_type' => $dataShipment['delivery_type'],
            'shipment_cost' => $dataShipment['shipment_cost'],
            'status' => 'pending',
            'order_id' => $dataShipment['order_id'],
            'address_id' => $dataShipment['address_id']
        ]);
    }
}
