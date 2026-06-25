@extends('Emails.layout')

@section('icon', '📍')
@section('title', 'Tu Pedido está en Camino')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>Tu pedido está en tránsito y se acerca cada vez más a tu ubicación.</p>

    <span class="status-badge" style="background-color: #17a2b8; color: white;">
        En Tránsito
    </span>

    <div class="order-details">
        <h3>Estado del Envío</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        @if($order->shipment)
        @if($order->shipment->tracking_number)
        <div class="detail-row">
            <span class="detail-label">Número de Seguimiento:</span>
            <span class="detail-value" style="font-weight: bold;">
                {{ $order->shipment->tracking_number }}
            </span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Estado Actual:</span>
            <span class="detail-value" style="color: #17a2b8; font-weight: bold;">
                En Tránsito hacia tu dirección
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Entrega Estimada:</span>
            <span class="detail-value">{{ now()->addDays(2)->format('d/m/Y') }}</span>
        </div>
        @endif
    </div>

    <div style="background-color: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #0c5460;">🚚 Tu paquete está en movimiento</p>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: #0c5460;">
            El transportista está haciendo su ruta de entrega. Te recomendamos estar atento para recibir tu pedido.
        </p>
    </div>

    <h3>Contenido del Paquete</h3>
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
        Te notificaremos cuando tu paquete sea entregado.
    </p>
@endsection
