<?php

namespace App\Http\Controllers\Api\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Mobile\ProductVariantMResource;
use App\Services\Api\v1\Mobile\VariantMService;
use Illuminate\Http\JsonResponse;

class VariantMController extends Controller
{
    public function __construct(
        protected VariantMService $service
    ) {}

    public function getVariantSku(string $sku): JsonResponse
    {
        $model = $this->service->getVariantSku($sku);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new ProductVariantMResource($model),
        ], 200);
    }
}
