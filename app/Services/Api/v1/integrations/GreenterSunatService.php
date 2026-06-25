<?php

namespace App\Services\Api\v1\integrations;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GreenterSunatService
{
    public function generateVoucher(Order $order, array $customerData, string $voucherType): array
    {
        if (!$this->hasGreenterConfig()) {
            Log::warning('Greenter configuracion incompleta. Se generara comprobante local.', [
                'order_id' => $order->id,
            ]);

            return $this->generateLocalVoucher($order, $customerData, $voucherType);
        }

        try {
            $invoice = $this->buildInvoice($order, $customerData, $voucherType);

            $see = new See();
            $see->setCertificate($this->loadCertificate());
            $see->setClaveSOL(
                (string) config('integrations.greenter.ruc'),
                (string) config('integrations.greenter.sol_user'),
                (string) config('integrations.greenter.sol_pass')
            );
            $see->setService($this->resolveSunatEndpoint());

            $result = $see->send($invoice);

            if (!$result || !$result->isSuccess()) {
                $error = $result?->getError();
                $message = $error?->getCode() . ' ' . $error?->getMessage();

                return [
                    'success' => false,
                    'error' => trim((string) $message) ?: 'SUNAT rechazo el comprobante',
                ];
            }

            $cdr = $result->getCdrResponse();
            if ($cdr && !$cdr->isAccepted()) {
                return [
                    'success' => false,
                    'error' => 'SUNAT: ' . ($cdr->getDescription() ?: 'Comprobante no aceptado'),
                ];
            }

            // SUNAT no retorna PDF: generamos PDF local para visualizacion/descarga del cliente.
            return $this->generateLocalVoucher($order, $customerData, $voucherType);
        } catch (\Throwable $e) {
            Log::error('Error integrando Greenter con SUNAT', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Error SUNAT/Greenter: ' . $e->getMessage(),
            ];
        }
    }

    protected function hasGreenterConfig(): bool
    {
        return filled(config('integrations.greenter.ruc'))
            && filled(config('integrations.greenter.sol_user'))
            && filled(config('integrations.greenter.sol_pass'))
            && filled(config('integrations.greenter.cert_path'));
    }

    protected function loadCertificate(): string
    {
        $certPath = (string) config('integrations.greenter.cert_path');
        $absolute = str_starts_with($certPath, '/') ? $certPath : base_path($certPath);

        if (!file_exists($absolute)) {
            throw new \RuntimeException('No existe certificado en: ' . $absolute);
        }

        $content = file_get_contents($absolute);
        if ($content === false) {
            throw new \RuntimeException('No se pudo leer el certificado: ' . $absolute);
        }

        return $content;
    }

    protected function resolveSunatEndpoint(): string
    {
        $env = strtolower((string) config('integrations.greenter.environment', 'beta'));

        return $env === 'production'
            ? SunatEndpoints::FE_PRODUCCION
            : SunatEndpoints::FE_BETA;
    }

    protected function buildInvoice(Order $order, array $customerData, string $voucherType): Invoice
    {
        $order->loadMissing('branchVariants.variant');

        $company = (new Company())
            ->setRuc((string) config('integrations.greenter.ruc'))
            ->setRazonSocial((string) config('integrations.greenter.razon_social'))
            ->setNombreComercial((string) config('integrations.greenter.nombre_comercial'))
            ->setAddress(
                (new Address())
                    ->setUbigueo((string) config('integrations.greenter.ubigeo'))
                    ->setDepartamento((string) config('integrations.greenter.departamento'))
                    ->setProvincia((string) config('integrations.greenter.provincia'))
                    ->setDistrito((string) config('integrations.greenter.distrito'))
                    ->setDireccion((string) config('integrations.greenter.direccion'))
            );

        $client = (new Client())
            ->setTipoDoc($this->mapDocumentType((string) ($customerData['document_type'] ?? 'DNI')))
            ->setNumDoc((string) ($customerData['document_number'] ?? ''))
            ->setRznSocial(
                data_get($customerData, 'customer.business_name')
                ?: trim((string) data_get($customerData, 'customer.name', '') . ' ' . (string) data_get($customerData, 'customer.last_name', ''))
            )
            ->setAddress((new Address())->setDireccion((string) data_get($customerData, 'customer.tax_address', '')))
            ->setEmail((string) data_get($customerData, 'customer.email', ''));

        $details = [];
        $mtoOperGravadas = 0.0;
        $mtoIGV = 0.0;
        $mtoImpVenta = 0.0;

        foreach ($order->branchVariants as $item) {
            $precio = (float) $item->pivot->unit_price;
            $cantidad = (float) $item->pivot->quantity;
            $valorUnit = round($precio / 1.18, 2);
            $valorVenta = round($valorUnit * $cantidad, 2);
            $igv = round(($precio - $valorUnit) * $cantidad, 2);
            $totalItem = round($precio * $cantidad, 2);

            $mtoOperGravadas += $valorVenta;
            $mtoIGV += $igv;
            $mtoImpVenta += $totalItem;

            $details[] = (new SaleDetail())
                ->setUnidad('NIU')
                ->setCantidad($cantidad)
                ->setCodProducto((string) $item->variant->sku)
                ->setDescripcion((string) $item->variant->full_name)
                ->setMtoValorUnitario($valorUnit)
                ->setMtoPrecioUnitario($precio)
                ->setMtoBaseIgv($valorVenta)
                ->setPorcentajeIgv(18)
                ->setIgv($igv)
                ->setTipAfeIgv('10')
                ->setTotalImpuestos($igv)
                ->setMtoValorVenta($valorVenta);
        }

        $sumOtrosCargos = (float) $order->shipment_cost;
        $totalImpuestos = round($mtoIGV, 2);
        $mtoImpVenta = round($mtoImpVenta + $sumOtrosCargos, 2);

        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101')
            ->setTipoDoc($this->mapVoucherType($voucherType))
            ->setSerie($this->generateSerie($voucherType, (string) $order->type_sale))
            ->setCorrelativo((string) $this->extractOrderNumber((string) $order->order_number))
            ->setFechaEmision(new DateTime())
            ->setTipoMoneda('PEN')
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas(round($mtoOperGravadas, 2))
            ->setMtoIGV(round($mtoIGV, 2))
            ->setTotalImpuestos($totalImpuestos)
            ->setValorVenta(round($mtoOperGravadas, 2))
            ->setSubTotal(round($mtoOperGravadas + $mtoIGV, 2))
            ->setSumOtrosCargos($sumOtrosCargos)
            ->setMtoImpVenta($mtoImpVenta)
            ->setDetails($details)
            ->setLegends([
                (new Legend())
                    ->setCode('1000')
                    ->setValue($this->amountLegend($mtoImpVenta)),
            ]);

        return $invoice;
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
                ? (string) config('integrations.greenter.serie_factura_online', 'F001')
                : (string) config('integrations.greenter.serie_boleta_online', 'B001');
        }

        return strtolower($voucherType) === 'factura'
            ? (string) config('integrations.greenter.serie_factura_store', 'F002')
            : (string) config('integrations.greenter.serie_boleta_store', 'B002');
    }

    protected function extractOrderNumber(string $orderNumber): int
    {
        $number = preg_replace('/[^0-9]/', '', $orderNumber);
        return (int) ltrim((string) $number, '0') ?: 1;
    }

    protected function amountLegend(float $amount): string
    {
        $formatted = number_format($amount, 2, '.', '');
        return 'SON ' . $formatted . ' SOLES';
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

        $order->voucher()->updateOrCreate([
            'order_id' => $order->id,
        ], [
            'type' => $voucherType,
            'voucher_number' => $order->order_number,
            'path' => $publicUrl,
            'order_id' => $order->id,
        ]);

        return [
            'success' => true,
            'path' => $publicUrl,
        ];
    }
}
