<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Admin\OptionProduct\StoreOptionProductRequest;
use App\Http\Requests\Api\v1\Admin\OptionProduct\StoreOptionProductValuesRequest;
use App\Http\Resources\Api\v1\Admin\OptionProductResource;
use App\Services\Api\v1\Admin\OptionProductService;
use Illuminate\Http\JsonResponse;

class OptionProductController extends Controller
{
    public function __construct(
        protected OptionProductService $service
    ) {}

    public function show(string $id): JsonResponse
    {
        $model = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new OptionProductResource($model),
        ], 200);
    }

    public function store(StoreOptionProductRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new OptionProductResource($response),
        ], 201);
    }

    public function addValues(StoreOptionProductValuesRequest $request): JsonResponse
    {
        $response = $this->service->addValues($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new OptionProductResource($response),
        ], 201);
    }

    public function getAllValues(int $productId, int $optionId): JsonResponse
    {
        $model = $this->service->getAllValues($productId, $optionId);
        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => $model,
        ], 200);
    }
}
