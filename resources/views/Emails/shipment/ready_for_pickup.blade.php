@extends('Emails.layout')

@section('icon', '✅')
@section('title', '¡Tu Orden está Lista para Recoger!')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>¡Excelentes noticias! Tu pedido está listo y esperándote en nuestra tienda.</p>

    <span class="status-badge" style="background-color: #28a745; color: white;">
        Listo para Recoger
    </span>

    <div class="order-details">
        <h3>Detalles de Recogida</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Preparación:</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        @if($order->shipment && $order->shipment->branch)
        <div class="detail-row">
            <span class="detail-label">Sucursal:</span>
            <span class="detail-value">{{ $order->shipment->branch->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Dirección:</span>
            <span class="detail-value">{{ $order->shipment->branch->address }}</span>
        </div>
        @endif
    </div>

    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #856404;">📍 Información Importante</p>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: #856404;">
            Por favor, trae tu <strong>número de orden (#{{ $order->order_number }})</strong> y una identificación válida al recoger tu pedido.
        </p>
    </div>

    <h3>Horario de Atención</h3>
    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
        <p style="margin: 5px 0;">📅 Lunes a Viernes: 9:30 AM - 7:00 PM</p>
        <p style="margin: 5px 0;">📅 Sábados: 9:30 AM - 6:00 PM</p>
    </div>

    <h3>Productos a Recoger</h3>
    <table class="products-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->branchVariants as $item)
            <tr>
                <td>{{ $item->variant->getFullNameAttribute() }}</td>
                <td>{{ $item->pivot->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
