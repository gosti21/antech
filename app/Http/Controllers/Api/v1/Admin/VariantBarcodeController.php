<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Admin\GenerateVariantBarcodeRequest;
use App\Services\Api\v1\Admin\VariantBarcodeService;
use Illuminate\Http\Request;

class VariantBarcodeController extends Controller
{
    public function __construct(
        private readonly VariantBarcodeService $service
    ) {}

    public function generate(GenerateVariantBarcodeRequest $request)
    {
        $pdf = $this->service->generateBarcodePDF($request->validated());
        return $pdf->download('codigos-barras.pdf');
    }
}
