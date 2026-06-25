<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Admin\UpdateOrderRequest;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Admin\OrderResource;
use App\Services\Api\v1\Admin\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service,
    ) {}

    public function index(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = OrderResource::collection(
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

    public function getPdf(int $id)
    {
        return $this->service->getPdf($id);
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Estado de orden actualizada',
            'data' => new OrderResource($model),
        ], 200);
    }
}
