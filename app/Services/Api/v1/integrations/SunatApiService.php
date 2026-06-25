<?php

namespace App\Services\Api\v1\integrations;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class SunatApiService
{
    public function generateVoucher(Order $order, array $customerData, string $voucherType): array
    {
        if (!$this->hasSunatConfig()) {
            return [
                'success' => false,
                'error' => 'Configuracion SUNAT incompleta. Define SUNAT_API_URL, SUNAT_API_TOKEN, SUNAT_RUC_EMISOR, SUNAT_SOL_USER y SUNAT_SOL_PASS',
            ];
        }

        $url = rtrim((string) config('integrations.sunat.base_url'), '/') . '/' . ltrim((string) config('integrations.sunat.emit_endpoint', '/invoices'), '/');

        $payload = $this->buildPayload($order, $customerData, $voucherType);

        $response = Http::withToken((string) config('integrations.sunat.token'))
            ->timeout(60)
            ->post($url, $payload);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => 'SUNAT API HTTP ' . $response->status() . ': ' . $response->body(),
            ];
        }

        $result = $response->json();

        $pdfPath = data_get($result, 'enlace_del_pdf')
            ?? data_get($result, 'pdf_url')
            ?? data_get($result, 'data.enlace_del_pdf')
            ?? data_get($result, 'data.pdf_url');

        if (!$pdfPath) {
            return [
                'success' => false,
                'error' => 'SUNAT API no devolvio la URL del PDF',
            ];
        }

        $order->voucher()->updateOrCreate([
            'order_id' => $order->id,
        ], [
            'type' => $voucherType,
            'voucher_number' => $order->order_number,
            'path' => $pdfPath,
            'order_id' => $order->id,
        ]);

        return [
            'success' => true,
            'path' => $pdfPath,
        ];
    }

    protected function hasSunatConfig(): bool
    {
        return filled(config('integrations.sunat.base_url'))
            && filled(config('integrations.sunat.token'))
            && filled(config('integrations.sunat.ruc'))
            && filled(config('integrations.sunat.sol_user'))
            && filled(config('integrations.sunat.sol_pass'));
    }

    protected function buildPayload(Order $order, array $customerData, string $voucherType): array
    {
        $order->loadMissing('branchVariants.variant');

        $items = [];
        $totalGravada = 0.0;
        $totalIgv = 0.0;
        $total = 0.0;

        foreach ($order->branchVariants as $detail) {
            $precioUnitario = (float) $detail->pivot->unit_price;
            $valorUnitario = round($precioUnitario / 1.18, 2);
            $cantidad = (float) $detail->pivot->quantity;

            $subtotal = round($valorUnitario * $cantidad, 2);
            $igv = round(($precioUnitario - $valorUnitario) * $cantidad, 2);
            $totalItem = round($precioUnitario * $cantidad, 2);

            $totalGravada += $subtotal;
            $totalIgv += $igv;
            $total += $totalItem;

            $items[] = [
                'codigo' => $detail->pivot->variant_sku,
                'descripcion' => $detail->variant->getFullNameAttribute(),
                'unidad_medida' => 'NIU',
                'cantidad' => $cantidad,
                'valor_unitario' => $valorUnitario,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $totalItem,
                'tipo_afectacion_igv' => '10',
            ];
        }

        $shipmentCost = (float) $order->shipment_cost;
        $total += $shipmentCost;

        return [
            'ambiente' => app()->environment('production') ? 'produccion' : 'beta',
            'emisor' => [
                'ruc' => config('integrations.sunat.ruc'),
                'usuario_sol' => config('integrations.sunat.sol_user'),
                'clave_sol' => config('integrations.sunat.sol_pass'),
            ],
            'comprobante' => [
                'tipo' => $this->mapVoucherType($voucherType),
                'serie' => $this->generateSerie($voucherType, (string) $order->type_sale),
                'numero' => $this->extractOrderNumber((string) $order->order_number),
                'fecha_emision' => now()->format('Y-m-d'),
                'moneda' => 'PEN',
            ],
            'cliente' => [
                'tipo_documento' => $this->mapDocumentType((string) ($customerData['document_type'] ?? 'DNI')),
                'numero_documento' => (string) ($customerData['document_number'] ?? ''),
                'denominacion' => data_get($customerData, 'customer.business_name')
                    ?: trim((string) data_get($customerData, 'customer.name', '') . ' ' . (string) data_get($customerData, 'customer.last_name', '')),
                'direccion' => (string) data_get($customerData, 'customer.tax_address', ''),
                'email' => (string) data_get($customerData, 'customer.email', ''),
            ],
            'totales' => [
                'gravada' => round($totalGravada, 2),
                'igv' => round($totalIgv, 2),
                'otros_cargos' => round($shipmentCost, 2),
                'total' => round($total, 2),
            ],
            'items' => $items,
        ];
    }

    protected function mapVoucherType(string $voucherType): string
    {
        return strtolower($voucherType) === 'factura' ? '01' : '03';
    }

    protected function mapDocumentType(string $documentType): string
    {
        return match (strtoupper($documentType)) {
            'RUC' => '6',
            'CE', 'CARNET_EXTRANJERIA' => '4',
            default => '1',
        };
    }

    protected function generateSerie(string $voucherType, string $typeSale): string
    {
        if ($typeSale === 'online') {
            return strtolower($voucherType) === 'factura'
                ? (string) config('integrations.sunat.serie_factura_online', 'F001')
                : (string) config('integrations.sunat.serie_boleta_online', 'B001');
        }

        return strtolower($voucherType) === 'factura'
            ? (string) config('integrations.sunat.serie_factura_store', 'F002')
            : (string) config('integrations.sunat.serie_boleta_store', 'B002');
    }

    protected function extractOrderNumber(string $orderNumber): int
    {
        $number = preg_replace('/[^0-9]/', '', $orderNumber);
        return (int) ltrim((string) $number, '0') ?: 1;
    }
}
