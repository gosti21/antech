<?php

namespace App\Repositories\Api\v1\Ia;

use App\Contracts\Api\v1\Ia\ProductIaInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductIaRepository implements ProductIaInterface
{
    public function getAllForAI(): Collection
    {
        return Product::query()
            // Solo productos con variantes
            ->has('variants')
            ->with([
                'brand:id,name',
                'subcategory.category:id,name',
                'subcategory:id,category_id,name',

                // Especificaciones
                'specifications' => function ($query) {
                    $query->select('specifications.id', 'specifications.name');
                },

                // Variantes con todo lo necesario
                'variants' => function ($query) {
                    $query->select('variants.id', 'variants.product_id', 'variants.sku', 'variants.selling_price')
                        ->with([
                            // Stock de sucursales
                            'branches:branches.id',

                            // Características (color, tamaño, etc.)
                            'optionProductValues.optionValue.option:id,name,type',
                            'optionProductValues.optionValue:id,option_id,value,description'
                        ]);
                }
            ])
            ->where('status', true)->get();
    }

    public function getByIdForAI(int $productId): ?Product
    {
        return Product::query()
            ->with([
                'brand:id,name',
                'subcategory.category:id,name',
                'subcategory:id,category_id,name',
                'specifications',
                'variants' => function ($query) {
                    $query->with([
                        'branches:branches.id',
                        'optionProductValues.optionValue.option:id,name,type',
                        'optionProductValues.optionValue:id,option_id,value'
                    ]);
                }
            ])
            ->where('status', true)->findOrFail($productId);
    }
}
