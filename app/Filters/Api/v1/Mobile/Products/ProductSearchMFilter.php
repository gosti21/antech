<?php

namespace App\Filters\Api\v1\Mobile\Products;

use Closure;

class ProductSearchMFilter
{
    public function handle($query, Closure $next)
    {
        $search = trim(request('search'));

        if (!$search) {
            return $next($query);
        }

        $query->where(function ($q) use ($search) {
            $q->where('name', 'ILIKE', "%{$search}%")
                ->orWhere('description', 'ILIKE', "%{$search}%")
                ->orWhereHas('brand', function ($b) use ($search) {
                    $b->where('name', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('specifications', function ($s) use ($search) {
                    // nombre de la especificación
                    $s->where('specifications.name', 'ILIKE', "%{$search}%")
                        // valor del pivot
                        ->orWhere('product_specification.value', 'ILIKE', "%{$search}%");
                });
        });

        return $next($query);
    }
}
