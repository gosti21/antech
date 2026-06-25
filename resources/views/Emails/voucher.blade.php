<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f3f4f6;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #10b981;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .order-box {
            background-color: #f9fafb;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .order-box h2 {
            margin: 0 0 15px 0;
            color: #10b981;
            font-size: 18px;
        }
        .order-detail {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .order-detail:last-child {
            border-bottom: none;
        }
        .order-detail strong {
            color: #374151;
        }
        .order-detail span {
            color: #6b7280;
        }
        .total-box {
            background-color: #ecfdf5;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .total-box .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
        }
        .message {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .message p {
            margin: 0;
            color: #92400e;
        }
        .attachment-info {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .attachment-info p {
            margin: 5px 0;
            color: #1e40af;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
            background-color: #f9fafb;
        }
        .footer p {
            margin: 5px 0;
        }
        .icon {
            display: inline-block;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ ¡Compra Confirmada!</h1>
            <p>Tu comprobante de pago está listo</p>
        </div>

        <div class="content">
            <p>Hola,</p>

            <p>Gracias por tu compra en <strong>{{ config('app.name') }}</strong>. Tu pedido ha sido procesado exitosamente.</p>

            <div class="order-box">
                <h2>📋 Detalles de tu Orden</h2>

                <div class="order-detail">
                    <strong>Número de Orden:</strong>
                    <span>{{ $orderNumber }}</span>
                </div>

                <div class="order-detail">
                    <strong>Fecha de Compra:</strong>
                    <span>{{ $createdAt }}</span>
                </div>

                <div class="order-detail">
                    <strong>Estado:</strong>
                    <span style="color: #10b981; font-weight: 600;">Confirmada</span>
                </div>
            </div>

            <div class="total-box">
                <div class="total-row">
                    <span>Total Pagado:</span>
                    <span>S/ {{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="attachment-info">
                <p><strong>📎 Comprobante Adjunto</strong></p>
                <p>Encontrarás tu comprobante electrónico adjunto a este correo en formato PDF.</p>
                <p style="font-size: 12px; margin-top: 10px;">Nombre del archivo: <em>Comprobante-{{ $orderNumber }}.pdf</em></p>
            </div>

            <div class="message">
                <p><strong>💡 Importante:</strong> Guarda este comprobante para tus registros. Puedes imprimirlo o guardarlo digitalmente.</p>
            </div>

            <p>Si tienes alguna pregunta sobre tu pedido, no dudes en contactarnos.</p>

            <p style="margin-top: 30px;">
                Saludos,<br>
                <strong>El equipo de {{ config('app.name') }}</strong>
            </p>
        </div>

        <div class="footer">
            <p>Este es un correo automático, por favor no responder directamente.</p>
            <p style="margin-top: 10px;">
                <a href="mailto:soporte@{{ str_replace(['http://', 'https://'], '', config('app.url')) }}" style="color: #4F46E5; text-decoration: none;">
                    Contactar Soporte
                </a>
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
