<?php

namespace App\Http\Resources\Api\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictShortResource extends JsonResource
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
            'country' => $this->province->department->country->name,
            'department' => $this->province->department->name,
            'province' => $this->whenLoaded('province', function () {
                return $this->province->name;
            }),
            'delivery_price' => $this->whenLoaded('shippingRate', function () {
                return $this->shippingRate->delivery_price;
            }),
            'status' => $this->status,
        ];;
    }
}
