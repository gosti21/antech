<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\VariantInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class VariantBarcodeService
{
    public function __construct(
        private readonly VariantInterface $repository
    ) {}

    public function generateBarcodePDF(array $data)
    {
        $generator = new BarcodeGeneratorPNG;
        $barcodeData = [];

        foreach ($data['items'] as $item) {
            $variant = $this->repository->getById($item['variant_id']);

            if (!$variant) {
                throw new NotFoundException();
            }

            // Generar código de barras en base64
            $barcode = base64_encode(
                $generator->getBarcode(
                    $variant->sku,
                    $generator::TYPE_CODE_128,
                    2,
                    60
                )
            );

            $barcodeData[] = [
                'variant_name' => $variant->product->name . ' - ' . $variant->product->model,
                'features' => $variant->optionProductValues->map(fn($f) => $f->optionValue->option->name . ': ' . $f->optionValue->description)->implode(' | '),
                'sku' => $variant->sku,
                'barcode' => $barcode,
                'quantity' => $item['quantity'],
            ];
        }

        $pdf = Pdf::loadView('Admin.barcodes.template', [
            'barcodes' => $barcodeData
        ]);

        // Configuración del PDF para etiquetas
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }
}
