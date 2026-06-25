<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\District\StoreDistrictRequest;
use App\Http\Requests\Api\v1\Admin\District\UpdateDistrictRequest;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Admin\DistrictResource;
use App\Http\Resources\Api\v1\Admin\DistrictShortResource;
use App\Services\Api\v1\Admin\DistrictService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<DepartmentService>
 */
class DistrictController extends BaseController
{
    public function __construct(DistrictService $service)
    {
        parent::__construct($service, DistrictResource::class);
    }

    public function index(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = DistrictShortResource::collection(
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

    public function store(StoreDistrictRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new DistrictResource($response),
        ], 201);
    }

    public function update(UpdateDistrictRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new DistrictResource($model),
        ], 200);
    }
}
