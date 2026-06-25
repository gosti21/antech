<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante {{ strtoupper($voucherType) }} - {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; margin: 0; }
        .subtitle { margin: 4px 0 0; color: #4b5563; }
        .section { margin-bottom: 18px; }
        .label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .totals { margin-top: 18px; width: 320px; margin-left: auto; }
        .totals td { border: none; padding: 4px 0; }
        .totals .final { font-size: 14px; font-weight: bold; border-top: 1px solid #9ca3af; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Comprobante de compra</p>
        <p class="subtitle">{{ strtoupper($voucherType) }} - Orden {{ $order->order_number }}</p>
    </div>

    <div class="section">
        <p><span class="label">Fecha:</span> {{ now()->format('d/m/Y H:i') }}</p>
        <p><span class="label">Cliente:</span>
            {{ data_get($customerData, 'customer.business_name') ?: trim(data_get($customerData, 'customer.name', '') . ' ' . data_get($customerData, 'customer.last_name', '')) }}
        </p>
        <p><span class="label">Documento:</span> {{ data_get($customerData, 'document_type') }} {{ data_get($customerData, 'document_number') }}</p>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->branchVariants as $item)
                    <tr>
                        <td>{{ $item->variant->full_name }}</td>
                        <td>{{ $item->variant->sku }}</td>
                        <td>{{ $item->pivot->quantity }}</td>
                        <td>S/ {{ number_format((float) $item->pivot->unit_price, 2) }}</td>
                        <td>S/ {{ number_format((float) ($item->pivot->unit_price * $item->pivot->quantity), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td style="text-align:right;">S/ {{ number_format((float) $order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>IGV</td>
            <td style="text-align:right;">S/ {{ number_format((float) $order->igv, 2) }}</td>
        </tr>
        <tr>
            <td>Envío</td>
            <td style="text-align:right;">S/ {{ number_format((float) $order->shipment_cost, 2) }}</td>
        </tr>
        <tr>
            <td class="final">Total</td>
            <td class="final" style="text-align:right;">S/ {{ number_format((float) $order->total, 2) }}</td>
        </tr>
    </table>
</body>
</html>