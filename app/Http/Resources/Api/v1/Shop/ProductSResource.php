<?php

namespace App\Http\Resources\Api\v1\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductSResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'model' => $this->model,
            'brand' => $this->brand->name,
            'variant' => $this->whenLoaded('variants', function () {
                $variant = $this->variants->first();

                return $variant ? [
                    'id'            => $variant->id,
                    'selling_price' => $variant->selling_price,
                    'stock'         => optional(
                        $variant->branches->first()?->pivot
                    )->stock ?? 0,
                    'image' => $variant->images->first()
                        ? Storage::url($variant->images->first()->path)
                        : null,
                ] : null;
            }),
        ];
    }
}
