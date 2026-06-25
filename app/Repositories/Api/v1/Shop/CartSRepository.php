<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\CartSInterface;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Model;

class CartSRepository implements CartSInterface
{
    public function getCartByUser(int $userId): ?Model
    {
        return Cart::with([
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
        ])->where('user_id', $userId)->where('status', 'active')->first();
    }

    public function getCartBySession(string $sessionId): ?Model
    {
        return Cart::with([
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
        ])->where('session_id', $sessionId)->where('status', 'active')->first();
    }

    /**
     * Obtener o crear carrito por user_id
     */
    public function getOrCreateByUserId(int $userId): Model
    {
        $cart = $this->getCartByUser($userId);
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $userId,
                'expires_at' => now()->addDays(30)
            ]);
        }

        return $cart->refresh();
    }

    /**
     * Obtener o crear carrito por session_id
     */
    public function getOrCreateBySessionId(string $sessionId): Model
    {
        $cart = $this->getCartBySession($sessionId);
        if (!$cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'expires_at' => now()->addDays(15)
            ]);
        }

        return $cart->refresh();
    }

    public function update(int $id, array $data): Model
    {
        $model = Cart::findOrFail($id);
        $model->update($data);
        return $model->refresh();
    }

    public function markAsMerged(Cart $cart): Model
    {
        return $this->update($cart->id, ['status' => 'merged']);
    }

    public function markAsCompleted(Model $cart): void
    {
        $cart->update([
            'status' => 'completed'
        ]);
    }

    public function validateStock(Model $cart): array
    {
        $errors = [];

        foreach ($cart->branchVariants as $item) {
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
}
