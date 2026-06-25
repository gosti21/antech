@extends('Emails.layout')

@section('icon', '🚚')
@section('title', '¡Tu Pedido ha sido Despachado!')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>¡Excelentes noticias! Tu pedido ha sido despachado y está en camino a tu dirección.</p>

    <span class="status-badge" style="background-color: #17a2b8; color: white;">
        Despachado
    </span>

    <div class="order-details">
        <h3>Información de Envío</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        @if($order->shipment)
        @if($order->shipment->tracking_number)
        <div class="detail-row">
            <span class="detail-label">Número de Seguimiento:</span>
            <span class="detail-value" style="font-weight: bold; color: #667eea;">
                {{ $order->shipment->tracking_number }}
            </span>
        </div>
        @endif
        @if($order->shipment->shippingCompany)
        <div class="detail-row">
            <span class="detail-label">Empresa de Envío:</span>
            <span class="detail-value">{{ $order->shipment->shippingCompany->name }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Dirección de Entrega:</span>
            <span class="detail-value">{{ $order->shipment->address }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Tiempo Estimado:</span>
            <span class="detail-value">3-5 días hábiles</span>
        </div>
        @endif
    </div>

    <h3>Productos Enviados</h3>
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
