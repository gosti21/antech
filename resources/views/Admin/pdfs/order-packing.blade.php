<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #000;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .info-row {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .info-value {
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .badge {
            display: inline-block;
            margin-top: 8px padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-pickup {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .badge-shipment {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    {{-- HEADER --}}
    <div class="header">
        <h1>GUÍA DE EMPAQUE</h1>
        <p>Orden #{{ $order->order_number }}</p>
        <p>Fecha: {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    {{-- INFORMACIÓN DEL CLIENTE --}}
    <div class="section">
        <div class="section-title">Cliente</div>
        <div class="info-row">
            <span class="info-label">Nombre/Razón Social:</span>
            <span class="info-value">{{ $order->checkout_snapshot['customer']['business_name'] ?? $order->checkout_snapshot['customer']['name'] . ' ' . $order->checkout_snapshot['customer']['last_name']}}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ $order->checkout_snapshot['document_type'] }}:</span>
            <span class="info-value">{{ $order->checkout_snapshot['document_number'] }}</span>
        </div>
    </div>

    {{-- TIPO DE ENTREGA --}}
    <div class="section">
        <div class="section-title">Tipo de Entrega</div>
        <div class="info-row">
            @if ($order->checkout_snapshot['delivery_type'] === 'store_pickup')
                <span class="badge badge-pickup">RECOJO EN TIENDA</span>
                <span class="info-value">{{ $order->branch->name ?? 'N/A' }}</span>
            @else
                <span class="badge badge-shipment">ENVÍO A DOMICILIO</span>
                <br>
                Domicilio: <span class="info-value">{{ $address->street ?? 'N/A' }}</span>
                <br>
                Referencia: <span class="info-value">{{ $address->reference ?? 'N/A' }}</span>
            @endif
        </div>
    </div>

    {{-- DATOS DE CONTACTO --}}
    <div class="section">
        <div class="section-title">Datos del Receptor del pedido</div>
        <div class="info-row">
            <span class="info-label">Nombre:</span>
            <span class="info-value">
                {{ $order->checkout_snapshot['receiver_info']['name'] }}
                {{ $order->checkout_snapshot['receiver_info']['last_name'] }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ $order->checkout_snapshot['receiver_info']['document_type'] }}:</span>
            <span class="info-value">{{ $order->checkout_snapshot['receiver_info']['document_number'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Teléfono:</span>
            <span class="info-value">{{ $order->checkout_snapshot['receiver_info']['phone'] }}</span>
        </div>
    </div>

        {{-- PRODUCTOS --}}
    <div class="section">
        <div class="section-title">Productos a Empacar</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">Cant.</th>
                    <th style="width: 50%">Producto</th>
                    <th style="width: 20%">SKU</th>
                    <th style="width: 20%">Verificado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->branchVariants as $item)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $item->pivot->quantity }}</td>
                    <td>{{ $item->pivot->product_name }}</td>
                    <td>{{ $item->pivot->variant_sku ?? 'N/A' }}</td>
                    <td style="text-align: center;">O</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Comprante de pago</div>
        <a href="{{ $order->voucher->path ?? '-' }}">Ver Voucher</a>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Este documento es una guía interna de empaque</p>
        <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>
