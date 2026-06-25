<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Admin\PaymentMethodRequest;
use App\Http\Resources\Api\v1\Admin\PaymentMethodResource;
use App\Services\Api\v1\Admin\PaymentMethodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function __construct(
        protected PaymentMethodService $service
    ) {}

    public function getAllList(): JsonResponse
    {
        $response = PaymentMethodResource::collection(
            $this->service->getAllList()
        );

        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $response,
        ], 200);
    }

    public function getById(int $id)
    {
        $response = new PaymentMethodResource($this->service->getById($id));
        return response()->json([
            'success' => true,
            'message' => 'Listado exitoso',
            'data' => $response,
        ], 200);
    }

    public function update(PaymentMethodRequest $request, int $id): JsonResponse
    {
        $model = $this->service->update($request->validated(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado',
            'data' => new PaymentMethodResource($model),
        ], 200);
    }
}
