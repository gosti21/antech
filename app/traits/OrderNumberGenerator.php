<?php

namespace App\traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait OrderNumberGenerator
{
        /**
     * Genera un número de orden secuencial
     *
     * @param string|null $prefix Prefijo para el número de orden (ej: 'ORD', 'PED')
     * @param int $length Longitud del número secuencial (sin contar el prefijo)
     * @param string|null $branchId ID de la sucursal para secuencia por sucursal
     * @return string
     */
    public function generateOrderNumber(?string $prefix = 'ORD', int $length = 8, ?string $branchId = null): string
    {
        $lockKey = 'order_number_generation';

        if ($branchId) {
            $lockKey .= "_branch_{$branchId}";
        }

        return Cache::lock($lockKey, 10)->block(5, function () use ($prefix, $length, $branchId) {
            return DB::transaction(function () use ($prefix, $length, $branchId) {
                $query = DB::table($this->getTable())
                    ->where('order_number', 'like', $prefix . '%');

                if ($branchId) {
                    $query->where('branch_id', $branchId);
                }

                // Detectar el driver de base de datos
                $driver = DB::connection()->getDriverName();
                $castType = $driver === 'pgsql' ? 'INTEGER' : 'UNSIGNED';

                $lastOrder = $query
                    ->orderByRaw("CAST(SUBSTRING(order_number, " . (strlen($prefix) + 1) . ") AS {$castType}) DESC")
                    ->first();

                $nextNumber = 1;

                if ($lastOrder) {
                    $lastNumber = (int) substr($lastOrder->order_number, strlen($prefix));
                    $nextNumber = $lastNumber + 1;
                }

                return $prefix . str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
            });
        });
    }
}
