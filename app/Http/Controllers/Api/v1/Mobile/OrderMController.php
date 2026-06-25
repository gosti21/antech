<?php

namespace App\Http\Controllers\Api\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Mobile\StoreOrderMRequest;
use App\Http\Requests\Api\v1\PaginationRequest;
use App\Http\Resources\Api\v1\Mobile\OrderMResource;
use App\Services\Api\v1\Mobile\OrderMService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderMController extends Controller
{
    public function __construct(
        protected OrderMService $orderService,
    ) {}

    public function getAll(PaginationRequest $request): JsonResponse
    {
        $perPage = $request->validated()['per_page'] ?? 15;

        $array = OrderMResource::collection(
            $this->orderService->getAll($perPage)
        )->response()->getData(true);

        $totals = $this->orderService->getTotals();

        return response()->json([
            'success' => true,
            'message' => 'Listado paginado exitoso',
            'data'    => $array['data'],
            'totals' => $totals,
            'links'   => $array['links'],
            'meta'    => $array['meta'],
        ], 200);
    }

    public function show(string $id): JsonResponse
    {
        $model = $this->orderService->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => new OrderMResource($model),
        ], 200);
    }

    public function store(StoreOrderMRequest $request)
    {
        $requestValidate = $request->validated();
        try {
            $userId = Auth::check() ? Auth::id() : null;
            $cartVerify = $this->orderService->verifyCart($userId);

            $result = $this->orderService->create($requestValidate, $cartVerify, $userId);

            // Descargar el PDF
            $client = new \GuzzleHttp\Client();
            $response = $client->get($result['pdf_url']);
            $pdfContent = $response->getBody()->getContents();
            $pdfBase64 = base64_encode($pdfContent);

            return response()->json([
                'success' => true,
                'message' => 'Orden creada',
                'voucher' => [
                    'filename' => 'comprobante_' . $result['order_number'] . '.pdf',
                    'content' => $pdfBase64,
                    'mime_type' => 'application/pdf'
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
