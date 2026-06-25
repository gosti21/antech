<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\OrderSInterface;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;

class OrderSRepository implements OrderSInterface
{
    public function getById(int $id): ?Model
    {
        return Order::with([
            'branchVariants' => function ($q) {
                $q->with([
                    'variant' => function ($q) {
                        $q->with([
                            'product.brand',
                            'images',
                            'optionProductValues.optionValue.option',
                        ]);
                    },
                ]);
            },
        ])->where('id', $id)->firstOrFail();
    }

    public function create(array $orderData): Model
    {
        return Order::create($orderData);
    }

    public function createDetails(Model $order, array $items): void
    {
        foreach ($items as $item) {
            OrderDetail::create([
                'product_name' => $item['product_name'],
                'variant_sku' => $item['variant_sku'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
                'branch_variant_id' => $item['branch_variant_id'],
                'order_id' => $order->id,
            ]);
        }
    }

    public function attachPaymentMethod(Model $order, int $paymentMethodId, array $paymentData): void
    {
        $order->paymentMethods()->attach($paymentMethodId, $paymentData);
    }

    public function validateStock(Model $order): array
    {
        $errors = [];

        foreach ($order->branchVariants as $item) {
            $stockDisponible = $item->stock;
            $cantidadSolicitada = $item->pivot->quantity;

            if ($stockDisponible < $cantidadSolicitada) {
                $productName = $item->variant->product->name ?? 'Producto';
                $variantSku = $item->variant->sku ?? '';

                $errors[] = [
                    'product' => $productName,
                    'sku' => $variantSku,
                    'available' => $stockDisponible,
                    'requested' => $cantidadSolicitada
                ];
            }
        }

        return $errors;
    }

    public function markAsPaid(Model $order, int $customerId): void
    {
        $order->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'customer_id' => $customerId
        ]);

        Order::where('user_id', $order->user_id)
            ->where('id', '!=', $order->id) // Importante: excluir la orden actual
            ->where('status', 'pending')
            ->update([
                'status' => 'cancelled',
                'checkout_snapshot' => null
            ]);
    }
}
