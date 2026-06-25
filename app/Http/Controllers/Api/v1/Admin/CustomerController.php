<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Admin\CustomerResource;
use App\Services\Api\v1\Admin\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $orderService,
    ) {}

    public function getAll(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = CustomerResource::collection(
            $this->orderService->getAll($perPage)
        )->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Listado paginado exitoso',
            'data'    => $array['data'],
            'links'   => $array['links'],
            'meta'    => $array['meta'],
        ], 200);
    }
}
