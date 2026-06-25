@extends('Emails.layout')

@section('icon', '💰')
@section('title', 'Reembolso Procesado')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>Tu orden ha sido reembolsada exitosamente. El dinero será devuelto a tu método de pago original.</p>

    <span class="status-badge" style="background-color: #17a2b8; color: white;">
        Reembolsada
    </span>

    <div class="order-details">
        <h3>Detalles del Reembolso</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Reembolso:</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Monto Reembolsado:</span>
            <span class="detail-value" style="color: #28a745; font-weight: bold;">
                S/. {{ number_format($order->total, 2) }}
            </span>
        </div>
    </div>

    <div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #155724;">⏱️ Tiempo de Procesamiento</p>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: #155724;">
            El reembolso aparecerá en tu cuenta en un plazo de <strong>5-10 días hábiles</strong>, dependiendo de tu banco o entidad financiera.
        </p>
    </div>

    <p style="color: #666; font-size: 14px; margin-top: 20px;">
        Si tienes alguna pregunta sobre el reembolso, no dudes en contactarnos.
    </p>
@endsection
