<?php

namespace App\Enums\Api\v1\Admin;

enum  TypeCustomer: string
{
    case COMPANY = 'company';
    case PEOPLE = 'people';

    public function label(): string
    {
        return match ($this) {
            self::COMPANY => 'Empresa',
            self::PEOPLE => 'Persona',
        };
    }
}
