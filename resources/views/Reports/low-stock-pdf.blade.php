<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Stock Bajo</title>
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
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #3B82F6;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .info-box {
            background-color: #F3F4F6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-box strong {
            color: #1F2937;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        thead {
            background-color: #3B82F6;
            color: white;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 11px;
        }
        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        tbody tr:hover {
            background-color: #F3F4F6;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .alert-low {
            color: #DC2626;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            background-color: #F0FDF4;
            border: 2px dashed #10B981;
            border-radius: 8px;
            margin-top: 20px;
        }
        .no-data h2 {
            color: #10B981;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .no-data p {
            color: #059669;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE STOCK BAJO</h1>
        <p>Productos que requieren reabastecimiento</p>
    </div>

    <div class="info-box">
        <p><strong>Fecha de generación:</strong> {{ $date }}</p>
        <p><strong>Sucursal:</strong> Principal (ID: 1)</p>
        <p><strong>Total de productos con stock bajo:</strong> {{ $items->count() }}</p>
    </div>

    @if($hasItems)
        <table>
            <thead>
                <tr>
                    <th class="text-center">N°</th>
                    <th>SKU</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th class="text-center">Stock Actual</th>
                    <th class="text-center">Stock Mínimo</th>
                    <th class="text-right">Precio S/.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['sku'] }}</td>
                    <td>{{ $item['product_name'] }}</td>
                    <td>{{ $item['brand'] }}</td>
                    <td class="text-center alert-low">{{ $item['current_stock'] }}</td>
                    <td class="text-center">{{ $item['min_stock'] }}</td>
                    <td class="text-right">{{ number_format($item['selling_price'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <h2>✓ Todos los productos están abastecidos</h2>
            <p>No hay productos con stock igual o menor al stock mínimo</p>
        </div>
    @endif

    <div class="footer">
        <p>Generado automáticamente por el Sistema de Gestión - {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
