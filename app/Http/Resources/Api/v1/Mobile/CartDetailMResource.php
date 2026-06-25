<?php

namespace App\Http\Resources\Api\v1\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CartDetailMResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'item_id' => $this->id,
            'branch_variant_id' => $this->branch_variant_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'variant' => $this->whenLoaded('branchVariant', function () {
                $variant = $this->branchVariant->variant;
                $product = $variant?->product;
                $image   = $variant?->images?->first();

                return [
                    'id' => $variant->id,
                    'product_id' => $product->id,
                    'name' => $product?->name,
                    'model' => $product?->model,
                    'brand' => $product?->brand?->name,
                    'stock' => $this->branchVariant->stock,
                    'image' => $image ? Storage::url($image->path) : null,
                    'features' => $variant && $variant->relationLoaded('optionProductValues')
                        ? $variant->optionProductValues->map(fn($feature) => [
                            'id' => $feature->option_value_id,
                            'option' => $feature->optionValue->option->name,
                            'type' => $feature->optionValue->option->type,
                            'value' => $feature->optionValue->value,
                            'description' => $feature->optionValue->description,
                        ])
                        : [],
                ];
            })
        ];
    }
}
