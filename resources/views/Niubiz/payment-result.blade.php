<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $success ? 'Pago Exitoso' : 'Error en el Pago' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Light mode (default) */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #1f2937;
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1e3a8a 0%, #581c87 100%);
                color: #f3f4f6;
            }

            .card {
                background: #1f2937 !important;
                border-color: #374151 !important;
            }

            .info-box {
                background: #374151 !important;
                border-color: #4b5563 !important;
            }

            .button {
                background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%) !important;
            }

            .button:hover {
                background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%) !important;
            }
        }

        .container {
            max-width: 600px;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            padding: 2rem;
            text-align: center;
        }

        .header.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .header.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .icon-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .icon {
            font-size: 3rem;
        }

        .icon.success {
            color: #10b981;
        }

        .icon.error {
            color: #ef4444;
        }

        .header h1 {
            color: white;
            font-size: 1.875rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        .content {
            padding: 2rem;
        }

        .info-box {
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .info-value {
            font-weight: 700;
            font-size: 1rem;
        }

        @media (prefers-color-scheme: dark) {
            .info-label {
                color: #9ca3af;
            }

            .info-row {
                border-bottom-color: #4b5563;
            }
        }

        .button {
            width: 100%;
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .button:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6b3fa0 100%);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .countdown {
            text-align: center;
            margin-top: 1rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        @media (prefers-color-scheme: dark) {
            .countdown {
                color: #9ca3af;
            }
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 640px) {
            .header h1 {
                font-size: 1.5rem;
            }

            .header p {
                font-size: 0.875rem;
            }

            .content {
                padding: 1.5rem;
            }

            .icon-container {
                width: 60px;
                height: 60px;
            }

            .icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="header {{ $success ? 'success' : 'error' }}">
                <div class="icon-container">
                    @if($success)
                        <div class="icon success">✓</div>
                    @else
                        <div class="icon error">✕</div>
                    @endif
                </div>
                <h1>{{ $message }}</h1>
                <p>{{ $description  }}</p>
            </div>

            <!-- Content -->
            <div class="content">
                @if(isset($orderId) || isset($transactionId))
                    <div class="info-box">
                        @if(isset($orderId))
                            <div class="info-row">
                                <span class="info-label">Número de orden</span>
                                <span class="info-value">#{{ $orderId }}</span>
                            </div>
                        @endif

                        @if(isset($voucherPath))
                            <div class="info-row">
                                <span class="info-label">Comprobante electrónico</span>
                                <span class="info-value">Disponible</span>
                            </div>
                        @endif

                        @if(isset($transactionId) && !str_starts_with((string) $transactionId, 'MOCK-'))
                            <div class="info-row">
                                <span class="info-label">ID de transacción</span>
                                <span class="info-value">{{ $transactionId }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                @if(isset($voucherPath))
                    <button class="button" onclick="downloadVoucher()" style="margin-bottom:1rem;">
                        Descargar comprobante
                    </button>
                @endif

                <button class="button" onclick="redirectNow()">
                    {{ $success ? 'Ir al inicio' : 'Reintentar' }}
                </button>

                <div class="countdown">
                    Serás redirigido en <span id="countdown">5</span> segundos...
                </div>
            </div>
        </div>
    </div>

    <script>
        const redirectUrl = "{{ $redirectUrl }}";
        const voucherDownloadUrl = @json(isset($orderId) ? route('vouchers.download', ['order' => $orderId]) : null);
        let countdown = 15;

        // Actualizar contador
        const countdownInterval = setInterval(() => {
            countdown--;
            document.getElementById('countdown').textContent = countdown;

            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = redirectUrl;
            }
        }, 1000);

        // Función para redirigir inmediatamente
        function redirectNow() {
            clearInterval(countdownInterval);
            window.location.href = redirectUrl;
        }

        function downloadVoucher() {
            if (!voucherDownloadUrl) {
                return;
            }

            clearInterval(countdownInterval);
            window.location.href = voucherDownloadUrl;
        }
    </script>
</body>
</html>
