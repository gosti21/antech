<?php

namespace App\Services\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\BranchVariantSInterface;
use App\Contracts\Api\v1\Shop\CartDetailSInterface;
use App\Contracts\Api\v1\Shop\CartSInterface;
use App\Exceptions\Api\v1\BadRequestException;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CartSService
{
    public function __construct(
        protected CartSInterface $cartRepository,
        protected UserSService $userService,
        protected BranchVariantSInterface $branchVariantRepository,
        protected CartDetailSInterface $cartDetailRepository,
    ) {}

    public function getCart(?int $userId, ?string $sessionId): ?Model
    {
        if ($userId) {
            $this->userService->getById($userId);
            return $this->cartRepository->getCartByUser($userId);
        }

        if ($sessionId) {
            return $this->cartRepository->getCartBySession($sessionId);
        }

        throw new BadRequestException(
            'No se puede acceder al carrito sin usuario ni sesión'
        );
    }

    public function getOrCreateCart(?int $userId, ?string $sessionId): Model
    {
        if ($userId) {
            $this->userService->getById($userId);
            return $this->cartRepository->getOrCreateByUserId($userId);
        }

        if ($sessionId) {
            return $this->cartRepository->getOrCreateBySessionId($sessionId);
        }

        throw new BadRequestException(
            'No se puede crear el carrito sin usuario ni sesión'
        );
    }

    public function addItemsToCart(Cart $cart, int $branchVariantId, int $quantity): Model
    {
        $branchVariant = $this->branchVariantRepository->getById($branchVariantId);
        if (!$this->branchVariantRepository->hasStockCart($branchVariantId, $quantity)) {
            throw new BadRequestException(
                "Stock insuficiente. Disponible: {$branchVariant->stock}"
            );
        }

        $cartDetail = $this->cartDetailRepository->findByBranchVariantId($cart->id, $branchVariantId);

        if($cartDetail) {
            $newQuantity = $cartDetail->quantity + $quantity;
            if (!$this->branchVariantRepository->hasStockCart($branchVariantId, $newQuantity)) {
                throw new BadRequestException(
                    "Stock insuficiente. Disponible: {$branchVariant->stock}"
                );
            }

            $cartDetail = $this->cartDetailRepository->update($cartDetail->id, $newQuantity);
        } else {
            $cartDetail = $this->cartDetailRepository->create($cart->id, [
                'branch_variant_id' => $branchVariantId,
                'quantity' => $quantity,
                'unit_price' => $branchVariant->variant->selling_price
            ]);
        }

        return $cartDetail->refresh();
    }

    public function updateItemQuantity(int $cartId, int $branchVariantId, int $quantity): Model
    {
        $cartDetail = $this->cartDetailRepository->findByBranchVariantId($cartId, $branchVariantId);

        $branchVariant = $this->branchVariantRepository->getById($branchVariantId);
        if (!$this->branchVariantRepository->hasStockCart($branchVariantId, $quantity)) {
            throw new BadRequestException(
                "Stock insuficiente. Disponible: {$branchVariant->stock}"
            );
        }
        $cartDetail = $this->cartDetailRepository->update($cartDetail->id, $quantity);

        return $cartDetail;
    }

    public function removeItem(int $cartId, int $branchVariantId): void
    {
        $this->cartDetailRepository->deleteItem($cartId, $branchVariantId);
    }

    public function deleteCart(int $cartId): void
    {
        $this->cartDetailRepository->deleteAll($cartId);
    }

    public function mergeGuestCart(string $sessionId, int $userId): ?Model
    {
        $guestCart = $this->cartRepository->getCartBySession($sessionId);
        if (!$guestCart) {
            return $this->getOrCreateCart($userId, null);
        }

        $userCart = $this->cartRepository->getOrCreateByUserId($userId);

        if($guestCart->auxBranchVariants()->exists()) {
            DB::beginTransaction();
            try {
                foreach($guestCart->auxBranchVariants as $guestDetail) {
                    $existingDetail = $this->cartDetailRepository->findByBranchVariantId(
                        $userCart->id,
                        $guestDetail->branch_variant_id
                    );

                    if($existingDetail) {
                        $newQuantity = $existingDetail->quantity + $guestDetail->quantity;
                        $branchVariant = $this->branchVariantRepository->getById($guestDetail->branch_variant_id);

                        if ($newQuantity > $branchVariant->stock) {
                            $newQuantity = $branchVariant->stock;
                        }
                        $this->cartDetailRepository->update($existingDetail->id, $newQuantity);
                        $this->cartDetailRepository->deleteById($guestDetail->id);
                    } else {
                        $this->cartDetailRepository->transferToCart($guestDetail->id, $userCart->id);
                    }
                }
                $this->cartRepository->markAsMerged($guestCart);
                DB::commit();
                return $this->cartRepository->getCartByUser($userCart->user_id);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
        $this->cartRepository->markAsMerged($guestCart);
        return $this->cartRepository->getCartByUser($userCart->user_id);
    }
}
