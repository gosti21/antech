<?php

namespace App\Enums\Api\v1\Admin;

enum OrderStatus: string
{
    case PENDING = 'pending'; // Creada, esperando confirmación de pago
    case CONFIRMED = 'confirmed'; // Pago confirmado, lista para procesar
    case PROCESSING = 'processing'; // Se está preparando el pedido
    case READY = 'ready'; // Lista para envío o pickup
    case COMPLETED = 'completed'; // Entregada/recogida exitosamente
    case CANCELLED = 'cancelled'; // Cancelada por cliente o sistema
    case REFUNDED = 'refunded'; // Devuelta y reembolsada

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pago pendiente',
            self::CONFIRMED => 'Confirmada',
            self::PROCESSING => 'Preparando pedido',
            self::READY => 'Pedido listo',
            self::COMPLETED => 'Entregado',
            self::CANCELLED => 'Cancelado',
            self::REFUNDED => 'Devuelto',
        };
    }
}
