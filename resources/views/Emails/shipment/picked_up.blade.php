@extends('emails.layout')

@section('icon', '✅')
@section('title', '¡Orden Recogida Exitosamente!')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>Confirmamos que has recogido tu pedido exitosamente. ¡Esperamos que disfrutes de tus productos!</p>

    <span class="status-badge" style="background-color: #28a745; color: white;">
        Recogida
    </span>

    <div class="order-details">
        <h3>Detalles de la Recogida</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Recogida:</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        @if($order->shipment && $order->shipment->branch)
        <div class="detail-row">
            <span class="detail-label">Sucursal:</span>
            <span class="detail-value">{{ $order->shipment->branch->name }}</span>
        </div>
        @endif
    </div>

    <h3>Productos Recogidos</h3>
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

    <div style="background-color: #f0f8ff; border-left: 4px solid #667eea; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #667eea;">💜 ¿Todo en orden?</p>
        <p style="margin: 10px 0 0 0; font-size: 14px;">
            Si tienes algún problema con tus productos, contáctanos dentro de las próximas 24 horas.
        </p>
    </div>

    <p style="color: #666; font-size: 14px; margin-top: 20px;">
        Gracias por tu compra. ¡Esperamos verte pronto!
    </p>
@endsection
