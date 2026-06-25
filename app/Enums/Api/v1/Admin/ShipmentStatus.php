<?php

namespace App\Enums\Api\v1\Admin;

enum ShipmentStatus: string
{
    case PENDING = 'pending'; // Creado, esperando preparación
    case PREPARING = 'preparing'; // Empaquetando/preparando
    case READYFORPICKUP = 'ready_for_pickup'; // Listo para recoger (solo store_pickup)
    case DISPATCHED = 'dispatched'; // Enviado con courier (solo shipment)
    case INTRANSIT = 'in_transit'; // En camino (solo shipment)
    case DELIVERED = 'delivered'; // Entregado en domicilio (solo shipment)
    case PICKEDUP = 'picked_up'; // Recogido en tienda (solo store_pickup)
    case FAILED = 'failed'; // Intento fallido de entrega
    case RETURNED = 'returned'; // Devuelto al almacén
    case CANCELLED = 'cancelled'; // Cancelacion manual

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::PREPARING => 'Preparando',
            self::READYFORPICKUP => 'Listo para recoger',
            self::DISPATCHED => 'En courier',
            self::INTRANSIT => 'En tránsito',
            self::DELIVERED => 'Entregado',
            self::PICKEDUP => 'Recogido',
            self::FAILED => 'Fallido',
            self::RETURNED => 'Retornado',
            self::CANCELLED => 'Cancelado',
        };
    }
}
