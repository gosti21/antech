<?php

namespace App\Http\Resources\Api\v1\Admin;

use App\Enums\Api\v1\Admin\ShipmentStatus;
use App\Enums\Api\v1\DeliveryType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order->order_number,
            'tracking_number' => $this->tracking_number ?? '-',
            'shippingCompany' => $this->shippingCompany->name ?? '-',
            'delivery_type' => DeliveryType::from($this->delivery_type)->label(),
            'shipment_cost' => $this->shipment_cost,
            'status' => ShipmentStatus::from($this->status)->label(),
            'status_en' => $this->status,
            'dispatched_at' => $this->dispatched_at ?? '-',
            'delivered_at' => $this->delivered_at ?? '-',
        ];
    }
}
