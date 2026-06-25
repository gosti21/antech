<?php

namespace App\Http\Resources\Api\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VariantResource extends JsonResource
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
            'selling_price' => $this->selling_price,
            'purcharse_price' => $this->purcharse_price,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'model' => $this->product->model,
                ];
            }),
            'status' => $this->status,
            'branch_stock' => $this->whenLoaded('branches', fn() => [
                'stock_min' => $this->branches->first()->pivot->stock_min,
                'stock' => $this->branches->first()->pivot->stock,
            ]),
            'features' => $this->whenLoaded('optionProductValues', function () {
                return $this->optionProductValues->map(function ($feature) {
                    return [
                        'id' => $feature->option_value_id,
                        'option' => $feature->optionValue->option->name,
                        'type   ' => $feature->optionValue->option->type,
                        'value' => $feature->optionValue->value,
                        'description' => $feature->optionValue->description,
                    ];
                });
            }),
            'img' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => Storage::url($image->path),
                    ];
                });
            }),
        ];
    }
}
