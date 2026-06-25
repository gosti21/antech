<?php

namespace App\Http\Controllers\Api\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Services\Api\v1\Mobile\CustomerMService;

class CustomerMController extends Controller
{
    public function __construct(
        protected CustomerMService $service
    ) {}

    public function searchDNI(string $dni)
    {
        if (!preg_match('/^\d{8}$/', $dni)) {
            return response()->json([
                'status'   => false,
                'message' => 'El DNI debe tener exactamente 8 dígitos',
            ], 422);
        }

        $result = $this->service->getByCustomerDNI($dni);

        $httpStatus = $result['status'] ? 200 : 404;

        return response()->json($result, $httpStatus);
    }

    public function searchRUC(string $ruc)
    {
        if (!preg_match('/^\d{11}$/', $ruc)) {
            return response()->json([
                'status'   => false,
                'message' => 'El RUC debe tener exactamente 11 dígitos',
            ], 422);
        }

        $result = $this->service->getBYCustomerRUC($ruc);

        $httpStatus = $result['status'] ? 200 : 404;

        return response()->json($result, $httpStatus);
    }
}
