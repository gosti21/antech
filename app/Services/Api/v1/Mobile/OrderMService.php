<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\CartMInterface;
use App\Contracts\Api\v1\Mobile\MovementMInterface;
use App\Contracts\Api\v1\Mobile\OrderMInterface;
use App\Exceptions\Api\v1\BadRequestException;
use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Cart;
use App\Models\PaymentMethod;
use App\Services\Api\v1\integrations\ElectronicInvoiceService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderMService
{
    protected $branchId = 1;

    public function __construct(
        protected CartMService $cartService,
        protected UserMService $userService,
        protected CustomerMService $customerService,
        protected OrderMInterface $orderRepository,
        protected MovementMInterface $movementRepository,
        protected CartMInterface $cartRepository,
        protected ElectronicInvoiceService $electronicInvoiceService,
    ) {}

    public function getAll(int $pagination = 16): LengthAwarePaginator
    {
        return $this->orderRepository->getAll($pagination);
    }

    public function getTotals(): array
    {
        return $this->orderRepository->getTotals();
    }

    public function getById(int $id): ?Model
    {
        try {
            return $this->orderRepository->getById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }

    public function verifyCart(int $userId): ?Model
    {
        $model = $this->cartService->getCart($userId);
        if(!$model){
            throw new BadRequestException(
                'No se puede hacer una orden sin un carrito'
            );
        }

        return $model;
    }

    public function create(array $data, Cart $cart, $userId): array
    {
        return DB::transaction(function () use ($data, $cart, $userId) {

            // 1. VALIDAR STOCK INICIAL
            $stockErrors = $this->cartRepository->validateStock($cart);

            if (!empty($stockErrors)) {
                $firstError = $stockErrors[0];
                throw new BadRequestException(
                    "Stock insuficiente para {$firstError['product']} ({$firstError['sku']}). " .
                        "Disponible: {$firstError['available']}, Solicitado: {$firstError['requested']}"
                );
            }

            // 2. PREPARAR DATOS DE LA ORDEN
            $employee = $this->userService->getById($userId);
            $customerId = $this->customerService->getOrCreate(
                $data['document_number'],
                $data['document_type'],
                $data['customer'],
                $userId
            );

            $orderTotals = $cart->calculateTotals()['total'];
            $priceNoIgv = round($orderTotals / 1.18, 2);
            $igv = round($orderTotals - $priceNoIgv, 2);

            // 3. CREAR LA ORDEN
            $order = $this->orderRepository->create([
                'type_sale' => 'store',
                'subtotal' => $priceNoIgv,
                'igv' => $igv,
                'total' => $orderTotals,
                'status' => 'completed',
                'payment_status' => 'paid',
                'employee_id' => $employee->employee->id,
                'customer_id' => $customerId,
                'branch_id' => $this->branchId,
                'cart_id' => $cart->id
            ]);

            // 4. CREAR DETALLES DE LA ORDEN
            $orderDetails = [];
            foreach ($cart->branchVariants as $item) {
                $orderDetails[] = [
                    'product_name' => $item->variant->full_name,
                    'variant_sku' => $item->variant->sku,
                    'unit_price' => $item->pivot->unit_price,
                    'quantity' => $item->pivot->quantity,
                    'subtotal' => ($item->pivot->unit_price * $item->pivot->quantity),
                    'branch_variant_id' => $item->id,
                ];
            }
            $this->orderRepository->createDetails($order, $orderDetails);

            // 5. CREAR MOVIMIENTO DE INVENTARIO
            $movement = $this->movementRepository->create([
                'movement_number' => $order->order_number,
                'type' => 'outflow',
                'reason' => 'sale',
                'detail_transaction' => 'Venta en tienda',
                'order_id' => $order->id
            ]);

            // 6. REGISTRAR INVENTARIO Y DESCONTAR STOCK CON LOCK
            foreach ($cart->branchVariants as $item) {
                // Verificar stock nuevamente con lock pesimista
                $hasStock = $this->movementRepository->verifyStockWithLock(
                    $item->id,
                    $item->pivot->quantity
                );

                if (!$hasStock) {
                    throw new BadRequestException(
                        "Stock insuficiente para {$item->variant->full_name}. " .
                            "Otro usuario realizó una compra simultánea."
                    );
                }

                // Registrar movimiento en tabla intermedia
                $this->movementRepository->attachInventoryMovement(
                    $movement,
                    $item->id,
                    $item->pivot->quantity
                );

                // Descontar stock
                $this->movementRepository->decrementStock(
                    $item->id,
                    $item->pivot->quantity
                );
            }

            // 7. REGISTRAR MÉTODO DE PAGO
            $paymentMethod = PaymentMethod::where('name', $data['payment_method'])->first();

            $this->orderRepository->attachPaymentMethod($order, $paymentMethod->id, [
                'amount' => $orderTotals,
                'transaction_id' => $data['payment_method_code'] ?? null
            ]);

            // 8. MARCAR CARRITO COMO COMPLETADO
            $this->cartRepository->markAsCompleted($cart);

            // 9. GENERAR COMPROBANTE EN NUBEFACT
            $voucherResult = $this->electronicInvoiceService->generateVoucher(
                $order,
                [
                    'document_type' => $data['document_type'],
                    'document_number' => $data['document_number'],
                    'customer' => $data['customer'],
                ],
                $data['type_voucher'] ?? 'boleta'
            );

            if (!$voucherResult['success']) {
                // Log el error pero no fallar la transacción
                $errorString = $voucherResult['error'];

                // Buscar desde el primer {
                $json = strstr($errorString, '{');

                $error = json_decode($json, true);

                throw new BadRequestException(
                    $error['errors']
                );

                Log::error('Error generando comprobante NubeFact', [
                    'order_id' => $order->id,
                    'error' => $voucherResult['error']
                ]);
            }

            return [
                'order_number' => $order->order_number,
                'pdf_url' => $voucherResult['path']
            ];
        });
    }
}
