@extends('Emails.layout')

@section('icon', '🎉')
@section('title', '¡Orden Completada!')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>¡Fantástico! Tu orden ha sido completada exitosamente. Esperamos que disfrutes de tus productos.</p>

    <span class="status-badge" style="background-color: #28a745; color: white;">
        Completada
    </span>

    <div class="order-details">
        <h3>Detalles de la Orden</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Completado:</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Total Pagado:</span>
            <span class="detail-value">S/. {{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    <h3>Productos Recibidos</h3>
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

    <p style="color: #666; font-size: 14px; margin-top: 20px;">
        Gracias por confiar en nosotros. ¡Esperamos verte pronto!
    </p>
@endsection
