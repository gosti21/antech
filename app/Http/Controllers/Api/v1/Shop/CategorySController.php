<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Shop\CategorySResource;
use App\Services\Api\v1\Shop\CategorySService;
use Illuminate\Http\JsonResponse;

class CategorySController extends Controller
{
    public function __construct(
        protected CategorySService $service
    ) {}

    public function getAll(): JsonResponse
    {
        $response = CategorySResource::collection(
            $this->service->getAll()
        );

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $response,
        ], 200);
    }

    public function show(string $id): JsonResponse
    {
        $model = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new CategorySResource($model),
        ], 200);
    }
}
