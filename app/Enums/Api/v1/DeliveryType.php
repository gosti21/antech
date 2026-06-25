<?php

namespace App\Enums\Api\v1;

enum  DeliveryType: string
{
    case SHIPMENT = 'shipment';
    case STOREPICKUP = 'store_pickup';

    public function label(): string
    {
        return match ($this) {
            self::SHIPMENT => 'Envio',
            self::STOREPICKUP => 'Recojo en Tienda',
        };
    }
}
