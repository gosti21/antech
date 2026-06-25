@extends('Emails.layout')

@section('icon', '✅')
@section('title', '¡Pedido Entregado!')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>¡Fantástico! Tu pedido ha sido entregado exitosamente en tu dirección. ¡Esperamos que disfrutes de tus productos!</p>

    <span class="status-badge" style="background-color: #28a745; color: white;">
        Entregado
    </span>

    <div class="order-details">
        <h3>Detalles de la Entrega</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Entrega:</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        @if($order->shipment)
        @if($order->shipment->tracking_number)
        <div class="detail-row">
            <span class="detail-label">Número de Seguimiento:</span>
            <span class="detail-value">{{ $order->shipment->tracking_number }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Dirección:</span>
            <span class="detail-value">{{ $order->shipment->address }}</span>
        </div>
        @endif
    </div>

    <h3>Productos Entregados</h3>
    <table class="products-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->branchVariants as $item)
            <tr>
                <td>{{ $item->variant->getFullNameAttribute() }}</td>
                <td>{{ $item->pivot->quantity }}</td>
                <td>S/. {{ number_format($item->pivot->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #856404;">📦 Verifica tu Pedido</p>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: #856404;">
            Por favor, verifica que todos los productos estén en buen estado. Si hay algún problema, contáctanos dentro de las próximas 24 horas.
        </p>
    </div>

    <p style="color: #666; font-size: 14px; margin-top: 20px;">
        Gracias por tu compra. ¡Esperamos verte pronto!
    </p>
@endsection
