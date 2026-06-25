<?php

namespace App\Http\Resources\Api\v1\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductVariantSResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $variantId = (int) $request->route('variantId');

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'model'        => $this->model,
            'description' => $this->description,
            'brand' => $this->whenLoaded('brand', fn() => $this->brand->name),
            'category' => $this->whenLoaded('subcategory', function () {
                return [
                    'id' => $this->subcategory->category->id,
                    'name' => $this->subcategory->category->name,
                ];
            }),

            'subcategory' => $this->whenLoaded('subcategory', function () {
                return [
                    'id' => $this->subcategory->id,
                    'name' => $this->subcategory->name,
                ];
            }),
            'specifications' => $this->whenLoaded('specifications', function () {
                return $this->specifications->map(function ($spec) {
                    return [
                        'name'  => $spec->name,
                        'value' => $spec->pivot->value,
                    ];
                });
            }),
            'selected_variant' => $this->whenLoaded('variants', function () use ($variantId) {
                $variant = $this->variants->firstWhere('id', $variantId);

                return $variant ? [
                    'id'    => $variant->id,
                    'branch_variant_id' => $variant->branches->first()->pivot->id,
                    'sku'   => $variant->sku,
                    'price' => $variant->selling_price,
                    'stock' => $variant->branches
                        ->first()
                        ->pivot
                        ->stock ?? 0,
                    'images' => $variant->images
                        ->map(fn($img) => [
                            'url' => Storage::url($img->path),
                        ])
                        ->values(),
                    'features' => $variant->optionProductValues->map(function ($feature) {
                        return [
                            'id' => $feature->option_value_id,
                            'option' => $feature->optionValue->option->name,
                            'type' => $feature->optionValue->option->type,
                            'value' => $feature->optionValue->value,
                            'description' => $feature->optionValue->description,
                        ];
                    })
                ] : null;
            }),
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'branch_variant_id' => $variant->branches->first()->pivot->id,
                        'features' => $variant->optionProductValues->map(function ($feature) {
                            return [
                                'id' => $feature->option_value_id,
                                'option' => $feature->optionValue->option->name,
                                'type' => $feature->optionValue->option->type,
                                'value' => $feature->optionValue->value,
                                'description' => $feature->optionValue->description,
                            ];
                        })
                    ];
                })->values();
            }),
        ];
    }
}
