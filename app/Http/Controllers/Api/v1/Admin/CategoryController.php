<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Api\v1\Admin\Category\UpdateCategoryRequest;
use App\Http\Resources\Api\v1\Admin\CategoryResource;
use App\Services\Api\v1\Admin\CategoryService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<CategoryService>
 */
class CategoryController extends BaseController
{
    public function __construct(CategoryService $service)
    {
        parent::__construct($service, CategoryResource::class);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new CategoryResource($response),
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new CategoryResource($model),
        ], 200);
    }

    public function getSubcategories(int $id)
    {
        $subcategories = $this->service->getSubcategories($id);
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $subcategories,
        ], 200);
    }
}
