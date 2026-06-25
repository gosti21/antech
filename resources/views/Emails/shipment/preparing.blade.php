@extends('emails.layout')

@section('icon', '📦')
@section('title', 'Preparando tu Envío')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>¡Buenas noticias! Estamos preparando tu pedido para el envío. Nuestro equipo está empacando cuidadosamente tus productos.</p>

    <span class="status-badge" style="background-color: #ffc107; color: #333;">
        Preparando Envío
    </span>

    <div class="order-details">
        <h3>Detalles del Pedido</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        @if($order->shipment)
        <div class="detail-row">
            <span class="detail-label">Tipo de Entrega:</span>
            <span class="detail-value">
                {{ $order->shipment->delivery_type === 'store_pickup' ? 'Recoger en Tienda' : 'Envío a Domicilio' }}
            </span>
        </div>
        @if($order->shipment->delivery_type !== 'store_pickup')
        <div class="detail-row">
            <span class="detail-label">Dirección de Envío:</span>
            <span class="detail-value">{{ $order->shipment->address }}</span>
        </div>
        @endif
        @endif
    </div>

    <h3>Productos</h3>
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
