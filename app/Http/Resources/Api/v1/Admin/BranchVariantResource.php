<?php

namespace App\Http\Resources\Api\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchVariantResource extends JsonResource
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
            'product' => $this->whenLoaded('variant', function () {
                return [
                    'id'    => $this->variant->id,
                    'name'  => $this->variant->product->name,
                    'model' => $this->variant->product->model,
                ];
            }),
            'features' => $this->whenLoaded('variant', function () {
                return $this->variant->optionProductValues->map(fn($feature) => [
                    'id' => $feature->option_value_id,
                    'description' => $feature->optionValue->description,
                ]);
            }),
        ];
    }
}
