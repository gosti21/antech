<?php

namespace App\Enums\Api\v1\Admin;

enum PaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Sin pagar',
            self::PAID => 'Pagado',
            self::REFUNDED => 'Anulado',
        };
    }
}
