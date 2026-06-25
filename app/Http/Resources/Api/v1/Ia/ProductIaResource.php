<?php

namespace App\Http\Resources\Api\v1\Ia;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductIaResource extends JsonResource
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
            'description' => $this->description ?? '',
            'brand' => $this->brand?->name ?? 'Sin marca',
            'category' => $this->subcategory?->category?->name ?? 'Sin categoría',
            'subcategory' => $this->subcategory?->name ?? 'Sin subcategoría',

            // Especificaciones (DPI, tamaño, peso, etc.)
            'specifications' => $this->specifications->map(function ($spec) {
                return [
                    'name' => $spec->name,
                    'value' => $spec->pivot->value,
                ];
            })->toArray(),

            // Variantes con sus características
            'variants' => $this->variants->map(function ($variant) {
                return [
                    'id' => $variant->branches->first()->pivot->id,
                    'sku' => $variant->sku,
                    'price' => (float) $variant->selling_price,

                    // Stock total (suma de todas las sucursales)
                    'stock' => $variant->branches->sum('pivot.stock'),

                    // Características (Color: Rojo, Tamaño: L, etc.)
                    'features' => $variant->optionProductValues->map(function ($feature) {
                        return [
                            'option' => $feature->optionValue->option->name,
                            'value' => $feature->optionValue->description,
                            'type' => $feature->optionValue->option->type,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];
    }
}
