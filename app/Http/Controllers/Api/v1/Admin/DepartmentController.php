<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Department\StoreDepartmentRequest;
use App\Http\Requests\Api\v1\Admin\Department\UpdateDepartmentRequest;
use App\Http\Resources\Api\v1\Admin\DepartmentResource;
use App\Services\Api\v1\Admin\DepartmentService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<DepartmentService>
 */
class DepartmentController extends BaseController
{
    public function __construct(DepartmentService $service)
    {
        parent::__construct($service, DepartmentResource::class);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new DepartmentResource($response),
        ], 201);
    }

    public function update(UpdateDepartmentRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new DepartmentResource($model),
        ], 200);
    }

    public function getProvinces(int $id)
    {
        $provinces = $this->service->getProvinces($id);
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $provinces,
        ], 200);
    }
}
