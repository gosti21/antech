<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Specification\StoreSpecificationRequest;
use App\Http\Requests\Api\v1\Admin\Specification\UpdateSpecificationRequest;
use App\Http\Resources\Api\v1\Admin\SpecificationResource;
use App\Services\Api\v1\Admin\SpecificationService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<SpecificationService>
 */
class SpecificationController extends BaseController
{
    public function __construct(SpecificationService $service)
    {
        parent::__construct($service, SpecificationResource::class);
    }

    public function store(StoreSpecificationRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new SpecificationResource($response),
        ], 201);
    }

    public function update(UpdateSpecificationRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new SpecificationResource($model),
        ], 200);
    }
}
