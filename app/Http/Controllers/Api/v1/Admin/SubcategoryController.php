<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Subcategory\StoreSubcategoryRequest;
use App\Http\Requests\Api\v1\Admin\Subcategory\UpdateSubcategoryRequest;
use App\Http\Resources\Api\v1\Admin\SubcategoryResource;
use App\Services\Api\v1\Admin\SubcategoryService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<SubcategoryService>
 */
class SubcategoryController extends BaseController
{
    public function __construct(SubcategoryService $service)
    {
        parent::__construct($service, SubcategoryResource::class);
    }

    public function store(StoreSubcategoryRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new SubcategoryResource($response),
        ], 201);
    }

    public function update(UpdateSubcategoryRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new SubcategoryResource($model),
        ], 200);
    }
}
