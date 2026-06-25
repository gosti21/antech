@extends('Emails.layout')

@section('icon', '↩️')
@section('title', 'Envío Retornado')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>Te informamos que tu envío ha sido retornado a nuestras instalaciones.</p>

    <span class="status-badge" style="background-color: #6c757d; color: white;">
        Envío Retornado
    </span>

    <div class="order-details">
        <h3>Detalles del Retorno</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Retorno:</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        @if($order->shipment && $order->shipment->tracking_number)
        <div class="detail-row">
            <span class="detail-label">Número de Seguimiento:</span>
            <span class="detail-value">{{ $order->shipment->tracking_number }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Estado de la Orden:</span>
            <span class="detail-value">Cancelada</span>
        </div>
    </div>

    <div style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #721c24;">🔄 Razones Comunes de Retorno</p>
        <ul style="margin: 10px 0 0 20px; font-size: 14px; color: #721c24;">
            <li>Múltiples intentos de entrega fallidos</li>
            <li>Rechazo del destinatario</li>
        </ul>
    </div>

    <p style="color: #666; font-size: 14px; margin-top: 20px;">
        Lamentamos los inconvenientes causados.
    </p>
@endsection
