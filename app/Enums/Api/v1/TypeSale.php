<?php

namespace App\Enums\Api\v1;

enum TypeSale: string
{
    case ONLINE = 'online';
    case STORE = 'store';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::STORE => 'Tienda física',
        };
    }
}
