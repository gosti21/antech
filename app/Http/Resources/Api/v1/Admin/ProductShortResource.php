<?php

namespace App\Http\Resources\Api\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShortResource extends JsonResource
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
            'model' => $this->model,
            'subcategory' => $this->whenLoaded('subcategory', function () {
                return $this->subcategory->name;
            }),
            'brand' => $this->whenLoaded('brand', function () {
                return $this->brand->name;
            }),
            'status' => $this->status,
        ];
    }
}
