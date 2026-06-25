<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Shop\ProductSResource;
use App\Http\Resources\Api\v1\Shop\ProductVariantSResource;
use App\Services\Api\v1\Shop\ProductSService;
use Illuminate\Http\JsonResponse;

class ProductSController extends Controller
{
    public function __construct(
        protected ProductSService $service
    ) {}

    public function getAll(): JsonResponse
    {
        $array = ProductSResource::collection(
            $this->service->getAll()
        )->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Listado paginado exitoso',
            'data'    => $array['data'],
            'links'   => $array['links'],
            'meta'    => $array['meta'],
        ], 200);
    }

    public function getAllLasts(): JsonResponse
    {
        $response = ProductSResource::collection(
            $this->service->getAllLasts()
        );

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data'    => $response,
        ], 200);
    }

    public function getAllVariants(string $productId, string $variantId): JsonResponse
    {
        $model = $this->service->getAllVariants($productId, $variantId);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new ProductVariantSResource($model),
        ], 200);
    }
}
