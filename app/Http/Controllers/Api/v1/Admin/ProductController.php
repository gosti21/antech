<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Product\StoreProductRequest;
use App\Http\Requests\Api\v1\Admin\Product\UpdateProductRequest;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Admin\ProductOptionsResource;
use App\Http\Resources\Api\v1\Admin\ProductResource;
use App\Http\Resources\Api\v1\Admin\ProductShortResource;
use App\Services\Api\v1\Admin\ProductService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<ProductService>
 */
class ProductController extends BaseController
{
    public function __construct(ProductService $service)
    {
        parent::__construct($service, ProductResource::class);
    }

    public function index(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = ProductShortResource::collection(
            $this->service->getAll($perPage)
        )->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Listado paginado exitoso',
            'data' => $array['data'],
            'links' => $array['links'],
            'meta' => $array['meta'],
        ], 200);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new ProductResource($response),
        ], 201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new ProductResource($model),
        ], 200);
    }

    public function getAllOptions(int $id): JsonResponse
    {
        $model = $this->service->getAllOptions($id);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new ProductOptionsResource($model),
        ], 200);
    }

    public function hasOptions(int $id): JsonResponse
    {
        $hasOptions = $this->service->hasOptions($id);
        return response()->json([
            'success' => true,
            'message' => $hasOptions
            ? 'Tiene opciones'
            : 'No tiene opciones',
        ], 200);
    }

    public function getAllOptionsShort(int $id): JsonResponse
    {
        $model = $this->service->getAllOptionsShort($id);
        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => $model,
        ], 200);
    }
}
