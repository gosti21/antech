<?php

namespace App\Enums\Api\v1;

enum  PaymentMethodType: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case YAPE = 'yape';
    case PLIN = 'plin';
    case TRANSFERS = 'transfers';
    case DEPOSITS = 'deposits';
    case OTHERS = 'others';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'efectivo',
            self::CARD => 'tarjeta',
            self::YAPE => 'yape',
            self::PLIN => 'plin',
            self::TRANSFERS => 'transferencias',
            self::DEPOSITS => 'depositos',
            self::OTHERS => 'otros',
        };
    }
}
