<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Brand\StoreBrandRequest;
use App\Http\Requests\Api\v1\Admin\Brand\UpdateBrandRequest;
use App\Http\Resources\Api\v1\Admin\BrandResource;
use App\Services\Api\v1\Admin\BrandService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<BrandService>
 */
class BrandController extends BaseController
{
    public function __construct(BrandService $service)
    {
        parent::__construct($service, BrandResource::class);
    }

    public function store(StoreBrandRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new BrandResource($response),
        ], 201);
    }

    public function update(UpdateBrandRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new BrandResource($model),
        ], 200);
    }
}
