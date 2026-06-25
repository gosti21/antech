<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Api\v1\Admin\Movement\StoreMovementRequest;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Admin\MovementResource;
use App\Http\Resources\Api\v1\Admin\MovementShortResource;
use App\Services\Api\v1\Admin\MovementService;
use Illuminate\Http\JsonResponse;

/**
 * @extends BaseController<MovementService>
 */
class MovementController extends BaseController
{
    public function __construct(MovementService $service)
    {
        parent::__construct($service, MovementResource::class);
    }

    public function index(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = MovementShortResource::collection(
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

    public function show(string $id): JsonResponse
    {
        $model = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new MovementResource($model),
        ], 200);
    }

    public function store(StoreMovementRequest $request): JsonResponse
    {
        $response = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new MovementResource($response),
        ], 201);
    }
}
