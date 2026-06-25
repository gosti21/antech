<?php

namespace App\Http\Controllers\Api\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Services\Api\v1\Mobile\VoucherMService;
use Illuminate\Http\JsonResponse;

class VoucherMController extends Controller
{
    public function __construct(
        protected VoucherMService $service,
    ) {}

    public function show(string $id): JsonResponse
    {
        $model = $this->service->getById($id);
        $client = new \GuzzleHttp\Client();
        $response = $client->get($model['path']);
        $pdfContent = $response->getBody()->getContents();
        $pdfBase64 = base64_encode($pdfContent);

        return response()->json([
            'success' => true,
            'message' => 'Exitoso',
            'data' => [
                'filename' => 'comprobante_' . $model['voucher_number'] . '.pdf',
                'content' => $pdfBase64,
                'mime_type' => 'application/pdf'
            ],
        ], 200);
    }
}
