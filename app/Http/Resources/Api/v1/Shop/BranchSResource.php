<?php

namespace App\Http\Resources\Api\v1\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchSResource extends JsonResource
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
            'address' => $this->whenLoaded('address', function () {
                return [
                    'id'            => $this->address->id,
                    'street' => $this->address->street . ' #' . $this->address->street_number,
                    'reference' => $this->address->reference,
                    'district' => $this->address->district->name
                ];
            }),
            'delivery_price' => 0,
        ];
    }
}
