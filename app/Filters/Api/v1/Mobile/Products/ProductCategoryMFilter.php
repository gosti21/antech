<?php

namespace App\Filters\Api\v1\Mobile\Products;

use Closure;

class ProductCategoryMFilter
{
    public function handle($query, Closure $next)
    {
        if ($categoryId = request('category')) {
            $query->whereHas('subcategory', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        return $next($query);
    }
}
