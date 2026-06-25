<?php

namespace App\Enums\Api\v1;

enum VoucherType: string
{
    case BOLETA = 'boleta';
    case FACTURA = 'factura';

    public function label(): string
    {
        return match ($this) {
            self::BOLETA => 'boleta',
            self::FACTURA => 'factura',
        };
    }
}
