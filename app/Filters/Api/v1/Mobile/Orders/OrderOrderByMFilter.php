<?php

namespace App\Filters\Api\v1\Mobile\Orders;

use Closure;

class OrderOrderByMFilter
{
    public function handle($query, Closure $next)
    {
        $orderBy  = request('order_by', 'created_at');
        $orderDir = strtolower(request('order_dir', 'desc'));

        // Validar dirección
        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'desc';
        }

        // Aplicar ordenamiento según el campo
        switch ($orderBy) {
            case 'order_number':
                $query->orderBy('order_number', $orderDir);
                break;

            case 'created_at':
            default:
                $query->orderBy('created_at', $orderDir);
                break;
        }

        return $next($query);
    }
}
