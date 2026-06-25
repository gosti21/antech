<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Cover\ReorderCoverRequest;
use App\Http\Requests\Api\v1\Admin\Cover\StoreCoverRequest;
use App\Http\Requests\Api\v1\Admin\Cover\UpdateCoverRequest;
use App\Http\Resources\Api\v1\Admin\CoverResource;
use App\Services\Api\v1\Admin\CoverService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<CoverService>
 */
class CoverController extends BaseController
{
    public function __construct(CoverService $service)
    {
        parent::__construct($service, CoverResource::class);
    }

    public function store(StoreCoverRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new CoverResource($response),
        ], 201);
    }

    public function update(UpdateCoverRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new CoverResource($model),
        ], 200);
    }

    public function reorder(ReorderCoverRequest $request): JsonResponse
    {
        $validated = $request->validated()['sorts'];
        $this->service->reorder($validated);

        return response()->json([
            'success' => true,
            'message' => 'Portadas reordenadas correctamente'
        ]);
    }
}
