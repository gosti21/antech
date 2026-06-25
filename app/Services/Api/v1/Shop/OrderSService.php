<?php

namespace App\Services\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\AddressSInterface;
use App\Contracts\Api\v1\Shop\CartSInterface;
use App\Contracts\Api\v1\Shop\MovementSInterface;
use App\Contracts\Api\v1\Shop\OrderSInterface;
use App\Events\OrderCreated;
use App\Exceptions\Api\v1\BadRequestException;
use App\Exceptions\Api\v1\InternalServerErrorException;
use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\Api\v1\integrations\ElectronicInvoiceService;
use App\Services\Api\v1\integrations\NiubizService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class OrderSService
{
    protected $branchId = 1;

    public function __construct(
        protected CartSInterface $cartRepository,
        protected ElectronicInvoiceService $electronicInvoiceService,
        protected UserSService $userService,
        protected CustomerSService $customerService,
        protected OrderSInterface $orderRepository,
        protected AddressSInterface $addressRespository,
        protected MovementSInterface $movementRepository,
        protected NiubizService $niubizService,
    ) {}

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
        $model = $this->cartRepository->getCartByUser($userId);
        if (!$model) {
            throw new BadRequestException(
                'No se puede hacer una orden sin un carrito'
            );
        }

        return $model;
    }

    public function generateOrder(Cart $cart, array $data, int $userId): array
    {
        try {
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

                $shipmentCost = $this->addressRespository->getByPrice($data['address_id']);
                $orderTotals = $cart->calculateTotals()['total'];
                $priceNoIgv = round($orderTotals / 1.18, 2);
                $igv = round($orderTotals - $priceNoIgv, 2);
                $total = $orderTotals + $shipmentCost;

                // 3. CREAR LA ORDEN
                $order = $this->orderRepository->create([
                    'subtotal' => $priceNoIgv,
                    'igv' => $igv,
                    'shipment_cost' => $shipmentCost,
                    'total' => $total,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'branch_id' => $this->branchId,
                    'user_id' => $userId,
                    'cart_id' => $cart->id,
                    'checkout_snapshot' => [
                        'type_voucher' => $data['type_voucher'],
                        'delivery_type' => $data['delivery_type'],
                        'document_type' => $data['document_type'],
                        'document_number' => $data['document_number'],
                        'customer' => $data['customer'],
                        'receiver_info' => $data['receiver_info'],
                        'address_id' => $data['address_id'],
                    ],
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
                $user = $this->userService->getById($userId);
                $tokenSession = $this->niubizService->generateSessionToken($total, $user);

                return [
                    'session_token' => $tokenSession,
                    'order_id' => $order->id,
                    'total' => $order->total
                ];
            });
        } catch (\Exception $e) {
            throw new InternalServerErrorException(
                'No se pudo crear la orden',
                $e->getMessage()
            );
        }
    }

    public function confirmOrder(string $transactionToken, Order $order, $userId): array
    {
        return DB::transaction(function () use ($transactionToken, $order, $userId) {
            $stockErrors = $this->orderRepository->validateStock($order);

            if (!empty($stockErrors)) {
                $firstError = $stockErrors[0];
                throw new BadRequestException(
                    "Stock insuficiente para {$firstError['product']} ({$firstError['sku']}). " .
                        "Disponible: {$firstError['available']}, Solicitado: {$firstError['requested']}"
                );
            }

            $niubizRes = $this->niubizService->paid($transactionToken, $order);

            if(isset($niubizRes['dataMap']) && ($niubizRes['dataMap']['ACTION_CODE'] == '000')){
                $snap = $order->checkout_snapshot;
                // 2. PREPARAR DATOS DE LA ORDEN
                $customerId = $this->customerService->getOrCreate(
                    $snap['document_number'],
                    $snap['document_type'],
                    $snap['customer'],
                    $userId
                );

                // 5. CREAR MOVIMIENTO DE INVENTARIO
                $movement = $this->movementRepository->create([
                    'movement_number' => $order->order_number,
                    'type' => 'outflow',
                    'reason' => 'sale',
                    'detail_transaction' => 'Venta online',
                    'order_id' => $order->id
                ]);

                // 6. REGISTRAR INVENTARIO Y DESCONTAR STOCK CON LOCK
                foreach ($order->branchVariants as $item) {
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
                $paymentMethod = PaymentMethod::where('name', 'niubiz')->first();
                $transactionId = data_get($niubizRes, 'order.transactionId')
                    ?? data_get($niubizRes, 'dataMap.TRANSACTION_ID')
                    ?? 'MOCK-NO-TX-ID';

                $this->orderRepository->attachPaymentMethod($order, $paymentMethod->id, [
                    'amount' => $order->total,
                    'transaction_id' => $transactionId
                ]);

                // 8. MARCAR CARRITO COMO COMPLETADO
                $cart = Cart::findOrFail($order->cart_id);
                $this->cartRepository->markAsCompleted($cart);

                //Actualizar order
                $this->orderRepository->markAsPaid($order, $customerId);

                $voucher = $order->voucher;
                if (!$voucher || !$voucher->path) {
                    $voucherResult = $this->electronicInvoiceService->generateVoucher(
                        $order,
                        [
                            'document_type' => $snap['document_type'],
                            'document_number' => $snap['document_number'],
                            'customer' => $snap['customer'],
                        ],
                        $snap['type_voucher'] ?? 'boleta'
                    );

                    if (!($voucherResult['success'] ?? false)) {
                        throw new BadRequestException(
                            $voucherResult['error'] ?? 'No se pudo generar el comprobante'
                        );
                    }

                    $order->refresh();
                }

                // 9. Delegar al evento->listener->job
                event(new OrderCreated(
                    $userId,
                    $order,
                    [
                        'document_type' => $snap['document_type'],
                        'document_number' => $snap['document_number'],
                        'customer' => $snap['customer'],
                    ],
                    [
                        'receiver_info' => $snap['receiver_info'],
                        'delivery_type' => $snap['delivery_type'],
                        'shipment_cost' => $order->shipment_cost,
                        'order_id' => $order->id,
                        'address_id' => $snap['address_id']
                    ],
                    $snap['type_voucher'] ?? 'boleta'
                ));

                $niubizRes['voucher_path'] = $order->voucher?->path;

                return $niubizRes;
            }

            return $niubizRes;
        });
    }
}
