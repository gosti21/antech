<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Admin\OptionValue\StoreOptionValueRequest;
use App\Http\Resources\Api\v1\Admin\OptionValueResource;
use App\Services\Api\v1\Admin\OptionValueService;
use Illuminate\Http\JsonResponse;

class OptionValueController extends Controller
{
    public function __construct(
        protected OptionValueService $service
    )
    { }

    public function show(string $id): JsonResponse
    {
        $model = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new OptionValueResource($model),
        ], 200);
    }

    public function store(StoreOptionValueRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new OptionValueResource($response),
        ], 201);
    }
}
