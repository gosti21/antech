<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Shop\Address\StoreAddressSRequest;
use App\Http\Requests\Api\v1\Shop\Address\UpdateAddressSRequest;
use App\Http\Resources\Api\v1\Shop\AddressExtendSResource;
use App\Http\Resources\Api\v1\Shop\AddressSResource;
use App\Services\Api\v1\Shop\AddressSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressSController extends Controller
{
    public function __construct(
        protected AddressSService $service
    ) {}

    public function index(): JsonResponse
    {
        $userId = Auth::check() ? Auth::id() : null;

        $response = AddressSResource::collection(
            $this->service->getAll($userId)
        );

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $response,
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $model = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => new AddressExtendSResource($model),
        ], 200);
    }

    public function favorite(): JsonResponse
    {
        $userId = Auth::check() ? Auth::id() : null;
        $model = $this->service->favorite($userId);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new AddressSResource($model),
        ], 200);
    }

    public function store(StoreAddressSRequest $request): JsonResponse
    {
        $userId = Auth::check() ? Auth::id() : null;
        $response = $this->service->create($request->validated(), $userId);

        return response()->json([
            'success' => true,
            'message' => 'Registro creado',
            'data' => new AddressSResource($response),
        ], 201);
    }

    public function update(UpdateAddressSRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new AddressExtendSResource($model),
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Dirección eliminada exitosamente'
        ], 200);
    }
}
