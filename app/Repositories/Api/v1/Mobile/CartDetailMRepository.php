<?php

namespace App\Repositories\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\CartDetailMInterface;
use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Database\Eloquent\Model;

class CartDetailMRepository implements CartDetailMInterface
{
    public function findByBranchVariantId(int $cartId, int $branchVariantId): ?Model
    {
        return CartDetail::where('branch_variant_id', $branchVariantId)
            ->where('cart_id', $cartId)
            ->first();
    }

    public function create(int $cartId, array $data): Model
    {
        Cart::findOrFail($cartId);

        $model = CartDetail::create([
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'cart_id' => $cartId,
            'branch_variant_id' => $data['branch_variant_id'],
        ]);

        return $model->refresh()->load([
            'branchVariant' => function ($q) {
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
        ]);
    }

    public function update(int $cartDetailId, int $newQuantity): Model
    {
        $model = CartDetail::findOrFail($cartDetailId);
        $model->update([
            'quantity' => $newQuantity
        ]);

        return $model->refresh()->load([
            'branchVariant' => function ($q) {
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
        ]);
    }

    public function deleteItem(int $cartId, int $branchVariantId): bool
    {
        $cart = Cart::findOrFail($cartId);
        return $cart->branchVariants()
            ->wherePivot('branch_variant_id', $branchVariantId)
            ->detach();
    }

    public function deleteAll(int $cartId): int
    {
        return Cart::findOrFail($cartId)
            ->branchVariants()
            ->detach();
    }

    public function deleteById(int $cartDetailId): bool
    {
        $model = CartDetail::findOrFail($cartDetailId);
        return $model->delete();
    }
}
