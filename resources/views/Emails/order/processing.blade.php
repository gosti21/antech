@extends('Emails.layout')

@section('icon', '⚙️')
@section('title', 'Tu Orden está en Proceso')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>Tu orden está siendo procesada por nuestro equipo. Estamos verificando el inventario y preparando todo para el envío.</p>

    <span class="status-badge" style="background-color: #ffc107; color: #333;">
        En Proceso
    </span>

    <div class="order-details">
        <h3>Detalles de la Orden</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden: </span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Estado de Envío: </span>
            <span class="detail-value">{{ $order->shipment ? 'Preparando' : 'Pendiente' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Total:</span>
            <span class="detail-value">S/. {{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    <h3>Resumen de Productos</h3>
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

    <p style="color: #666; font-size: 14px; margin-top: 20px;">
        Te notificaremos cuando tu orden esté lista para ser enviada o recogida.
    </p>
@endsection
