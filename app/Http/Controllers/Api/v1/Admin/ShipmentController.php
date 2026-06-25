<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Admin\UpdateShipmentRequest;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Admin\ShipmentResource;
use App\Services\Api\v1\Admin\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $service,
    ) {}

    public function index(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = ShipmentResource::collection(
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

    public function update(UpdateShipmentRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Estado de orden actualizada',
            'data' => new ShipmentResource($model),
        ], 200);
    }
}
