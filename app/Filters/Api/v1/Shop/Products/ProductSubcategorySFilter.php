<?php

namespace App\Filters\Api\v1\Shop\Products;

use Closure;

class ProductSubcategorySFilter
{
    public function handle($query, Closure $next)
    {
        $subcategoryId = request('subcategory');
        $categoryId    = request('category');

        if ($subcategoryId) {
            $query->whereHas('subcategory', function ($q) use ($subcategoryId, $categoryId) {
                $q->where('id', $subcategoryId);

                // 👇 VALIDACIÓN DE ANIDACIÓN
                if ($categoryId) {
                    $q->where('category_id', $categoryId);
                }
            });
        }

        return $next($query);
    }
}
