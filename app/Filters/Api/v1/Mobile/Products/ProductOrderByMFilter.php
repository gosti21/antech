<?php

namespace App\Filters\Api\v1\Mobile\Products;

use Closure;

class ProductOrderByMFilter
{
    public function handle($query, Closure $next)
    {
        $orderBy  = request('order_by', 'name');
        $orderDir = strtolower(request('order_dir', 'asc'));

        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'asc';
        }

        if (!request()->has('order_by')) {
            $query->orderBy('id', 'asc');
        }

        switch ($orderBy) {
            case 'price':
                $query->withMin('variants', 'selling_price')
                    ->orderBy('variants_min_selling_price', $orderDir);
                break;

            case 'created_at':
                $query->orderBy('created_at', $orderDir);
                break;

            case 'name':
            default:
                $query->orderBy('name', $orderDir);
                break;
        }

        return $next($query);
    }
}
