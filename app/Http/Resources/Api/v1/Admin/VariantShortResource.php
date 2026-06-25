<?php

namespace App\Http\Resources\Api\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VariantShortResource extends JsonResource
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
            'sku' => $this->sku,
            'status' => $this->status,
            'img' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => Storage::url($image->path),
                    ];
                });
            }),
            'features' => $this->whenLoaded('optionProductValues', function () {
                return $this->optionProductValues->map(function ($feature) {
                    return [
                        'id' => $feature->option_value_id,
                        'description' => $feature->optionValue->description,
                    ];
                });
            }),
        ];
    }
}
