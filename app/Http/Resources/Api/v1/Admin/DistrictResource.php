<?php

namespace App\Http\Resources\Api\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
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
            'name' => $this->name,
            'country' => [
                'id' => $this->province->department->country->id,
                'name' => $this->province->department->country->name,
            ],
            'department' => [
                'id' => $this->province->department->id,
                'name' => $this->province->department->name,
            ],
            'province' => $this->whenLoaded('province', function () {
                return [
                    'id' => $this->province->id,
                    'name' => $this->province->name,
                ];
            }),
            'shipping_rate' => $this->whenLoaded('shippingRate', function () {
                return [
                    'id' => $this->shippingRate->id,
                    'delivery_price' => $this->shippingRate->delivery_price,
                    'min_delivery_days' => $this->shippingRate->min_delivery_days,
                    'max_delivery_days' => $this->shippingRate->max_delivery_days,
                ];
            }),
            'status' => $this->status,
        ];
    }
}
