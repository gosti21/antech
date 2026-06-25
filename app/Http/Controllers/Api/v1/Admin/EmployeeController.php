<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Admin\Employee\StoreEmployeeRequest;
use App\Http\Requests\Api\v1\Admin\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Admin\EmployeeResource;
use App\Http\Resources\Api\v1\Admin\EmployeeShortResource;
use App\Services\Api\v1\Admin\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @extends BaseController<EmployeeService>
 */
class EmployeeController extends BaseController
{
    public function __construct(EmployeeService $service)
    {
        parent::__construct($service, EmployeeResource::class);
    }

    public function index(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = EmployeeShortResource::collection(
            $this->service->getAll($perPage)
        )->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Listado paginado exitoso',
            'data' => $array['data'],
            'links' => $array['links'],
            'meta' => $array['meta'],
        ], 200);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new EmployeeResource($response),
        ], 201);
    }

    public function update(UpdateEmployeeRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new EmployeeResource($model),
        ], 200);
    }
}
