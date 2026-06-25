<?php

namespace App\Filters\Api\v1\Mobile\Orders;

use Carbon\Carbon;
use Closure;

class OrderDateMFilter
{
    public function handle($query, Closure $next)
    {
        // Si viene el parámetro 'date', filtrar por esa fecha
        if (request()->has('date') && request('date')) {
            $date = request('date');

            // Validar que sea una fecha válida
            try {
                $parsedDate = Carbon::parse($date);
                $query->whereDate('created_at', $parsedDate);
            } catch (\Exception $e) {
                // Si la fecha es inválida, usar hoy por defecto
                $query->whereDate('created_at', Carbon::today());
            }
        } else {
            // Si no viene fecha, usar hoy por defecto
            $query->whereDate('created_at', Carbon::today());
        }

        return $next($query);
    }
}
