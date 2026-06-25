<?php

namespace App\Enums\Api\v1\Admin;

enum  EmployeeRole: string
{
    case USER = 'user';
    case EMPLOYEE = 'employee';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'Usuario',
            self::EMPLOYEE => 'Trabajador',
            self::ADMIN => 'Administrador',
        };
    }
}
