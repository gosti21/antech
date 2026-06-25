<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\Exceptions\Api\v1\BadRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Shop\StoreOrderSRequest;
use App\Services\Api\v1\Shop\OrderSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderSController extends Controller
{
    public function __construct(
        protected OrderSService $service,
    ) {}

    public function orderCreate(StoreOrderSRequest $request){
        $requestValidate = $request->validated();
        try {
            $userId = Auth::check() ? Auth::id() : null;
            $cartVerify = $this->service->verifyCart($userId);

            if ($cartVerify->calculateTotals()['total'] == 0.0) {
                throw new BadRequestException(
                    'No se puede crear un token session sin items en el carrito'
                );
            }

            $order = $this->service->generateOrder($cartVerify, $requestValidate, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Orden creada',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function confirmOrder(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) {
            return view('Niubiz.payment-result', [
                'success' => false,
                'message' => 'Datos incompletos: order_id no recibido',
                'redirectUrl' => config('app.url_front') . '/checkout/payment'
            ]);
        }

        $transactionToken = $request->input('transactionToken');
        if (!$transactionToken) {
            return view('Niubiz.payment-result', [
                'success' => false,
                'message' => 'Datos incompletos: transactionToken no recibido',
                'redirectUrl' => config('app.url_front') . '/checkout/payment'
            ]);
        }

        try {
            $orderVerify = $this->service->getById($orderId);

            $res = $this->service->confirmOrder($transactionToken, $orderVerify, $orderVerify->user_id);

            if (isset($res['dataMap']) && $res['dataMap']['ACTION_CODE'] === '000') {
                return view('Niubiz.payment-result', [
                    'success' => true,
                    'message' => '¡Pago exitoso!',
                    'description' => 'Tu compra ha sido confirmada. Ya puedes descargar tu comprobante electrónico.',
                    'orderId' => $orderId,
                    'transactionId' => $res['dataMap']['TRANSACTION_ID'] ?? null,
                    'voucherPath' => $res['voucher_path'] ?? null,
                    'redirectUrl' => config('app.url_front')
                ]);
            } else {
                $actionDescription = data_get($res, 'error.action_description', 'Pago rechazado');
                
                return view('Niubiz.payment-result', [
                    'success' => false,
                    'message' => 'Pago rechazado',
                    'description' => $actionDescription,
                    'orderId' => $orderId,
                    'redirectUrl' => config('app.url_front') . '/checkout/payment'
                ]);
            }
        } catch (\Exception $e) {
            return view('Niubiz.payment-result', [
                'success' => false,
                'message' => 'Error al procesar el pago',
                'description' => $e->getMessage(),
                'redirectUrl' => config('app.url_front') . '/checkout/payment'
            ]);
        }
    }
}
