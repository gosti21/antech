<?php

namespace App\Filters\Api\v1\Mobile\Products;

use Closure;

class ProductPriceMFilter
{
    public function handle($query, Closure $next)
    {
        $min = request('priceMin');
        $max = request('priceMax');

        if ($min || $max) {
            $query->whereHas('variants', function ($q) use ($min, $max) {
                if ($min) {
                    $q->where('selling_price', '>=', $min);
                }
                if ($max) {
                    $q->where('selling_price', '<=', $max);
                }
            });
        }

        return $next($query);
    }
}
