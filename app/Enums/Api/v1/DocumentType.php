<?php

namespace App\Enums\Api\v1;

enum  DocumentType: string
{
    case DNI = 'DNI';
    case CE = 'CE';
    case RUC = 'RUC';

    public function label(): string
    {
        return match ($this) {
            self::DNI => 'DNI',
            self::CE => 'CE',
            self::RUC => 'RUC',
        };
    }
}
