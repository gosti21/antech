<?php

namespace App\Services\Api\v1\integrations;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;

class NubeFactService
{
    protected function getClient(string $typeSale): Client
    {
        $token = $typeSale === 'online'
            ? config('integrations.nubefact.token_online')
            : config('integrations.nubefact.token_store');

        return new Client([
            'base_uri' => config('integrations.nubefact.base_url'),
            'headers'  => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 60,
        ]);
    }

    /**
     * Generar comprobante de pago en NubeFact
     */
    public function generateVoucher(Order $order, array $customerData, string $voucherType): array
    {
        try {
            if (! $this->hasNubeFactConfig($order->type_sale)) {
                return $this->generateLocalVoucher($order, $customerData, $voucherType);
            }

            $client = $this->getClient($order->type_sale);

            $data = $this->prepareVoucherData($order, $customerData, $voucherType);

            $response = $client->post('', [
                'json' => $data
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // Guardar datos del comprobante en la orden
            $this->saveVoucherData($order, $result, $voucherType);

            return [
                'success' => true,
                'path' => $result['enlace_del_pdf'] ?? null
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Preparar datos para enviar a NubeFact
     */
    protected function prepareVoucherData(Order $order, array $customerData, string $voucherType): array
    {
        /* dd($order->toArray()); */
        // Mapear tipo de comprobante
        $tipoComprobante = $this->mapVoucherType($voucherType);

        // Mapear tipo de documento del cliente
        $tipoDocumento = $this->mapDocumentType($customerData['document_type']);

        // Generar serie según tipo de comprobante
        $serie = $this->generateSerie($voucherType, $order->type_sale);

        // Preparar items
        $items = [];
        $totalGravada = 0;
        $totalIgv = 0;
        $total = 0;

        foreach ($order->branchVariants as $detail) {
            $precioUnitario = floatval($detail->pivot->unit_price);
            $valorUnitario = round($precioUnitario / 1.18, 2);
            $cantidad = $detail->pivot->quantity;

            $subtotal = round($valorUnitario * $cantidad, 2);
            $igv = round(($precioUnitario - $valorUnitario) * $cantidad, 2);
            $totalItem = round($precioUnitario * $cantidad, 2);

            // Acumular totales
            $totalGravada += $subtotal;
            $totalIgv += $igv;
            $total += $totalItem;

            $items[] = [
                "unidad_de_medida" => "NIU", // NIU = Unidad (bienes)
                "codigo" => $detail->pivot->variant_sku,
                "descripcion" => $detail->variant->getFullNameAttribute(),
                "cantidad" => $cantidad,
                "valor_unitario" => $valorUnitario,
                "precio_unitario" => $precioUnitario,
                "subtotal" => $subtotal,
                "tipo_de_igv" => 1, // 1 = Gravado - Operación Onerosa
                "igv" => $igv,
                "total" => $totalItem
            ];
        }

        $shipmentCost = $order->shipment_cost;
        $total = $total + $shipmentCost;

        return [
            "operacion" => "generar_comprobante",
            "tipo_de_comprobante" => $tipoComprobante,
            "serie" => $serie,
            "numero" => $this->extractOrderNumber($order->order_number),
            "sunat_transaction" => 1, // 1 = Venta Interna
            "cliente_tipo_de_documento" => $tipoDocumento,
            "cliente_numero_de_documento" => $customerData['document_number'],
            "cliente_denominacion" => data_get($customerData, 'customer.business_name')
                ?? trim(
                    data_get($customerData, 'customer.name', '') . ' ' .
                        data_get($customerData, 'customer.last_name', '')
                ),
            "cliente_direccion" => $customerData['customer']['tax_address'] ?? '',
            "cliente_email" => '',
            "fecha_de_emision" => now()->format('Y-m-d'),
            "fecha_de_vencimiento" => '',
            "moneda" => 1, // 1 = PEN (Soles)
            "porcentaje_de_igv" => 18,
            "total_gravada" => round($totalGravada, 2),
            "total_igv" => round($totalIgv, 2),
            "total_otros_cargos" => round($shipmentCost, 2),
            "total" => round($total, 2),
            "items" => $items,
        ];
    }

    /**
     * Mapear tipo de comprobante
     */
    protected function mapVoucherType(string $voucherType): int
    {
        return match (strtolower($voucherType)) {
            'factura' => 1,
            'boleta' => 2,
            default => 2, // Por defecto boleta
        };
    }

    /**
     * Mapear tipo de documento
     */
    protected function mapDocumentType(string $documentType): int
    {
        return match (strtoupper($documentType)) {
            'DNI' => 1,
            'CE', 'CARNET_EXTRANJERIA' => 4,
            'RUC' => 6,
            default => 1, // Por defecto DNI
        };
    }

    /**
     * Generar serie según tipo de comprobante
     */
    protected function generateSerie(string $voucherType, string $typeSale): string
    {
        if($typeSale === 'online') {
            return match (strtolower($voucherType)) {
                'factura' => config('integrations.nubefact.serie_factura_online', 'FFF1'),
                'boleta' => config('integrations.nubefact.serie_boleta_online', 'BBB1'),
                default => config('integrations.nubefact.serie_boleta_online', 'BBB1'),
            };
        }
        return match (strtolower($voucherType)) {
            'factura' => config('integrations.nubefact.serie_factura_store', 'FFF2'),
            'boleta' => config('integrations.nubefact.serie_boleta_store', 'BBB2'),
            default => config('integrations.nubefact.serie_boleta_store', 'BBB2'),
        };
    }

    /**
     * Extraer solo el número de la orden
     * De "ORD00000001" extraer "1"
     */
    protected function extractOrderNumber(string $orderNumber): int
    {
        // Remover el prefijo y los ceros a la izquierda
        $number = preg_replace('/[^0-9]/', '', $orderNumber);
        return (int) ltrim($number, '0') ?: 1;
    }

    /**
     * Guardar datos del comprobante en la orden
     */
    protected function saveVoucherData(Order $order, array $response, string $voucherType): void
    {
        $order->voucher()->updateOrCreate([
            'order_id' => $order->id,
        ], [
            'type' => $voucherType,
            'voucher_number' => $order->order_number,
            'path' => $response['enlace_del_pdf'] ?? null,
            'order_id' => $order->id
        ]);
    }

    protected function hasNubeFactConfig(string $typeSale): bool
    {
        $baseUrl = config('integrations.nubefact.base_url');
        $token = $typeSale === 'online'
            ? config('integrations.nubefact.token_online')
            : config('integrations.nubefact.token_store');

        return filled($baseUrl) && filled($token);
    }

    protected function generateLocalVoucher(Order $order, array $customerData, string $voucherType): array
    {
        $pdf = Pdf::loadView('Vouchers.customer-voucher-pdf', [
            'order' => $order->loadMissing('branchVariants.variant'),
            'customerData' => $customerData,
            'voucherType' => $voucherType,
        ]);

        $fileName = sprintf('vouchers/%s-%s.pdf', strtolower($voucherType), $order->order_number);

        Storage::disk('public')->put($fileName, $pdf->output());

        $publicUrl = Storage::disk('public')->url($fileName);

        $this->saveVoucherData($order, [
            'enlace_del_pdf' => $publicUrl,
        ], $voucherType);

        return [
            'success' => true,
            'path' => $publicUrl,
        ];
    }
}
