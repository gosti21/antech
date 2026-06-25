<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Province\StoreProvinceRequest;
use App\Http\Requests\Api\v1\Admin\Province\UpdateProvinceRequest;
use App\Http\Resources\Api\v1\Admin\ProvinceResource;
use App\Services\Api\v1\Admin\ProvinceService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<ProvinceService>
 */
class ProvinceController extends BaseController
{
    public function __construct(ProvinceService $service)
    {
        parent::__construct($service, ProvinceResource::class);
    }

    public function store(StoreProvinceRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new ProvinceResource($response),
        ], 201);
    }

    public function update(UpdateProvinceRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new ProvinceResource($model),
        ], 200);
    }
}
