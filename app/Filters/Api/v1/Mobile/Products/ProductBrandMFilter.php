<?php

namespace App\Filters\Api\v1\Mobile\Products;

use Closure;

class ProductBrandMFilter
{
    //validar que la brand_id pasada exista
    public function handle($query, Closure $next)
    {
        if ($brandId = request('brand')) {
            $query->where('brand_id', $brandId);
        }

        return $next($query);
    }
}
