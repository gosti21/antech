<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #10B981;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #10B981;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .info-box {
            background-color: #F3F4F6;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .info-box div {
            flex: 1;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-box strong {
            color: #1F2937;
        }
        .summary-box {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .summary-box h2 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .summary-box .total {
            font-size: 28px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        thead {
            background-color: #10B981;
            color: white;
        }
        th {
            padding: 10px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        td {
            padding: 8px 6px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 10px;
        }
        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        .status-completed {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status-cancelled {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        .footer-total {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
        }
        .footer-total strong {
            color: #3B82F6;
            font-size: 18px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            background-color: #FEF3C7;
            border: 2px dashed #F59E0B;
            border-radius: 8px;
            margin-top: 20px;
        }
        .no-data h2 {
            color: #D97706;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .no-data p {
            color: #92400E;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE VENTAS</h1>
        <p>Detalle de transacciones realizadas</p>
    </div>

    <div class="info-box">
        <div>
            <p><strong>Periodo:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
            <p><strong>Total de órdenes:</strong> {{ $sales->count() }}</p>
        </div>
        <div style="text-align: right;">
            <p><strong>Generado:</strong> {{ $generatedDate }}</p>
            <p><strong>Estado:</strong> Solo órdenes pagadas</p>
        </div>
    </div>

    <div class="summary-box">
        <h2>Total de Ventas</h2>
        <div class="total">S/. {{ number_format($totalSales, 2) }}</div>
    </div>

    @if($sales->count() > 0)
        <table>
            <thead>
                <tr>
                    <th class="text-center">N°</th>
                    <th>Nro. Orden</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Método de Pago</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">IGV</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $index => $sale)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sale['order_number'] }}</td>
                    <td>{{ $sale['date'] }}</td>
                    <td>{{ $sale['customer'] }}</td>
                    <td>{{ $sale['payment_methods'] }}</td>
                    <td class="text-right">S/. {{ number_format($sale['subtotal'], 2) }}</td>
                    <td class="text-right">S/. {{ number_format($sale['igv'], 2) }}</td>
                    <td class="text-right"><strong>S/. {{ number_format($sale['total'], 2) }}</strong></td>
                    <td class="text-center">
                        <span class="status-badge status-{{ $sale['status'] }}">
                            {{ ucfirst($sale['status']) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer-total">
            <p>TOTAL GENERAL: <strong>S/. {{ number_format($totalSales, 2) }}</strong></p>
        </div>
    @else
        <div class="no-data">
            <h2>⚠ No hay ventas en este periodo</h2>
            <p>No se encontraron órdenes pagadas entre las fechas seleccionadas</p>
        </div>
    @endif

    <div class="footer">
        <p>Generado automáticamente por el Sistema de Gestión - {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
