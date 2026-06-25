<?php

namespace App\Http\Resources\Api\v1\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressExtendSResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) {
            return [];
        }

        return [
            "id" => $this->id,
            "favorite" => $this->favorite,
            "street" => $this->street,
            "street_number" => $this->street_number,
            "reference" => $this->reference,
            "favorite" => $this->favorite,
            "district" => [
                'id' => $this->district->id,
                'name' => $this->district->name,
            ],
            "province" => [
                'id' => $this->district->province->id,
                'name' => $this->district->province->name
            ],
            "department" => [
                'id' => $this->district->province->department->id,
                'name' => $this->district->province->department->name
            ],
            "delivery_price" => $this->district->shippingRate->delivery_price,
        ];
    }
}
