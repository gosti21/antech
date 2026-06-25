@extends('Emails.layout')

@section('icon', '❌')
@section('title', 'Orden Cancelada')

@section('content')
    <p>Hola <strong>{{ $order->user->name }}</strong>,</p>

    <p>Te informamos que tu orden ha sido cancelada.</p>

    <span class="status-badge" style="background-color: #dc3545; color: white;">
        Cancelada
    </span>

    <div class="order-details">
        <h3>Detalles de la Orden</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Orden:</span>
            <span class="detail-value">#{{ $order->order_number }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Cancelación:</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Monto:</span>
            <span class="detail-value">S/. {{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    @if($order->payment_status === 'paid')
    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; font-weight: 600; color: #856404;">💰 Información de Reembolso</p>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: #856404;">
            Si realizaste un pago, el reembolso será procesado en un plazo de 5-10 días hábiles.
        </p>
    </div>
    @endif

    <h3>Productos de la Orden Cancelada</h3>
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
        Lamentamos los inconvenientes. Si tienes alguna pregunta sobre la cancelación, no dudes en contactarnos.
    </p>
@endsection
