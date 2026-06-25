<?php

namespace App\Http\Resources\Api\v1\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductVariantMResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $variantId = (int) $request->route('variantId');
        $variantSku = $request->route('sku');

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'model'        => $this->model,
            'description' => $this->description,
            'brand' => $this->whenLoaded('brand', fn() => $this->brand->name),
            'specifications' => $this->whenLoaded('specifications', function () {
                return $this->specifications->map(function ($spec) {
                    return [
                        'name'  => $spec->name,
                        'value' => $spec->pivot->value,
                    ];
                });
            }),
            'selected_variant' => $this->whenLoaded('variants', function () use ($variantId, $variantSku) {
                $variant = $variantId
                    ? $this->variants->firstWhere('id', $variantId)
                    : $this->variants->firstWhere('sku', $variantSku);

                return $variant ? [
                    'id'    => $variant->id,
                    'branch_variant_id' => $variant->branches->first()->pivot->id,
                    'sku'   => $variant->sku,
                    'price' => $variant->selling_price,
                    'stock' => optional(
                        $variant->branches->first()?->pivot
                    )->stock ?? 0,
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
                        'id'        => $variant->id,
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
