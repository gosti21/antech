<?php

namespace App\Enums\Api\v1\Admin;

enum  MovementReasonType: string
{
    case SALE = 'sale';
    case PURCHASE = 'purchase';
    case RETURN = 'return';
    case ADJUSTMENT = 'adjustment';
    case TRANSFER = 'transfer';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SALE => 'Venta',
            self::PURCHASE => 'Compra',
            self::RETURN => 'Devolución',
            self::ADJUSTMENT => 'Ajuste',
            self::TRANSFER => 'Transferencia',
            self::OTHER => 'Otro',
        };
    }
}
