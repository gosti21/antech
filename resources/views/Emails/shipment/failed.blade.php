@extends('Emails.layout')

@section('icon', '⚠️')
@section('title', 'Problema con la Entrega')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>Lamentamos informarte que hubo un problema al intentar entregar tu pedido.</p>

    <span class="status-badge" style="background-color: #dc3545; color: white;">
        Entrega Fallida
    </span>

    <div class="order-details">
        <h3>Detalles del Envío</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        @if($order->shipment)
        @if($order->shipment->tracking_number)
        <div class="detail-row">
            <span class="detail-label">Número de Seguimiento:</span>
            <span class="detail-value">{{ $order->shipment->tracking_number }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Dirección de Entrega:</span>
            <span class="detail-value">{{ $order->shipment->address }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Fecha del Intento:</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #721c24;">⚠️ Posibles Razones</p>
        <ul style="margin: 10px 0 0 20px; font-size: 14px; color: #721c24;">
            <li>Nadie disponible para recibir el paquete</li>
            <li>Acceso restringido a la ubicación</li>
        </ul>
    </div>

    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #856404;">📋 Próximos Pasos</p>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: #856404;">
            Nuestro equipo de soporte se pondrá en contacto contigo en las próximas <strong>24 horas</strong> para coordinar un nuevo intento de entrega o acordar una solución.
        </p>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: #856404;">
            También puedes contactarnos directamente para resolver este problema más rápido.
        </p>
    </div>
    
    <p style="color: #666; font-size: 14px; margin-top: 20px;">
        Lamentamos los inconvenientes. Haremos todo lo posible para resolver esto rápidamente.
    </p>
@endsection
