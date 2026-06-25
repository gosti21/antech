<?php

namespace App\Http\Resources\Api\v1\Admin;

use App\Enums\Api\v1\Admin\OrderStatus;
use App\Enums\Api\v1\Admin\PaymentStatus;
use App\Enums\Api\v1\DeliveryType;
use App\Enums\Api\v1\TypeSale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'date' => $this->created_at->format('d-m-Y'),
            'type_sale' => TypeSale::from($this->type_sale)->label(),
            'total' => $this->total,
            'status' => OrderStatus::from($this->status)->label(),
            'status_en' => $this->status,
            'payment_status' => PaymentStatus::from($this->payment_status)->label(),
            'customer' => $this->customer->name ?? $this->customer->business_name,
            'delivery_type' => DeliveryType::from($this->shipment->delivery_type ?? 'store_pickup')->label(),
        ];
    }
}
