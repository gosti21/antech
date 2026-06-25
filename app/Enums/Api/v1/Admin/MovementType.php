<?php

namespace App\Enums\Api\v1\Admin;

enum MovementType : string
{
    case INFLOW = 'inflow';
    case OUTFLOW = 'outflow';

    public function label(): string
    {
        return match ($this) {
            self::INFLOW => 'Entrada',
            self::OUTFLOW => 'Salida',
        };
    }
}
