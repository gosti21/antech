@extends('Emails.layout')

@section('icon', '✅')
@section('title', '¡Orden Confirmada!')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>¡Excelentes noticias! Tu orden ha sido confirmada y estamos preparando todo para ti.</p>

    <span class="status-badge" style="background-color: #28a745; color: white;">
        Confirmada
    </span>

    <div class="order-details">
        <h3>Detalles de la Orden</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha:</span>
            <span class="detail-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Total:</span>
            <span class="detail-value">S/. {{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    <h3>Productos</h3>
    <table class="products-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
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

    @if($order->shipment)
    <div class="order-details">
        <h3>Información de Envío</h3>
        <div class="detail-row">
            <span class="detail-label">Tipo de Entrega:</span>
            <span class="detail-value">
                {{ $order->shipment->delivery_type === 'store_pickup' ? 'Recoger en Tienda' : 'Envío a Domicilio' }}
            </span>
        </div>
        @if($order->shipment->delivery_type !== 'store_pickup')
        <div class="detail-row">
            <span class="detail-label">Dirección:</span>
            <span class="detail-value">{{ $order->shipment->address }}</span>
        </div>
        @endif
    </div>
    @endif

    <p style="color: #666; font-size: 14px; margin-top: 20px;">
        Te mantendremos informado sobre el estado de tu orden.
    </p>
@endsection
