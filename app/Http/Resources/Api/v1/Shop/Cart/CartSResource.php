<?php

namespace App\Http\Resources\Api\v1\Shop\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CartSResource extends JsonResource
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
            'session_id' => $this->session_id ? true : false,
            'status' => $this->status,
            'user_id' => $this->user_id ? true : false,
            'detail_cart' => $this->whenLoaded('branchVariants', function () {
                return $this->branchVariants->map(function ($item) {

                    $variant = $item->variant;
                    $product = $variant?->product;
                    $image   = $variant?->images?->first();

                    return [
                        'item_id' => $item->pivot->id,
                        'branch_variant_id' => $item->pivot->branch_variant_id,
                        'quantity' => $item->pivot->quantity,
                        'unit_price' => $item->pivot->unit_price,

                        'variant' => $variant ? [
                            'id' => $variant->id,
                            'sku' => $variant->sku,
                            'product_id' => $product->id,
                            'name' => $product?->name,
                            'model' => $product?->model,
                            'brand' => $product?->brand?->name,
                            'stock' => $item->stock,
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
                        ] : null,
                    ];
                });
            }),
        ];
    }
}
