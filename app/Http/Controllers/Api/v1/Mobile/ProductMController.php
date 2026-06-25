<?php

namespace App\Http\Controllers\Api\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Mobile\ProductMResource;
use App\Http\Resources\Api\v1\Mobile\ProductVariantMResource;
use App\Services\Api\v1\Mobile\ProductMService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductMController extends Controller
{
    public function __construct(
        protected ProductMService $service
    ) {}

    public function getAll(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = ProductMResource::collection(
            $this->service->getAll($perPage)
        )->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Listado paginado exitoso',
            'data'    => $array['data'],
            'links'   => $array['links'],
            'meta'    => $array['meta'],
        ], 200);
    }

    public function getAllVariants(string $productId, string $variantId): JsonResponse
    {
        $model = $this->service->getAllVariants($productId, $variantId);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new ProductVariantMResource($model),
        ], 200);
    }
}
