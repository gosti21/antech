<?php

namespace App\Services\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\BranchVariantMInterface;
use App\Contracts\Api\v1\Mobile\CartDetailMInterface;
use App\Contracts\Api\v1\Mobile\CartMInterface;
use App\Exceptions\Api\v1\BadRequestException;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Model;

class CartMService
{
    public function __construct(
        protected CartMInterface $cartRepository,
        protected UserMService $userService,
        protected BranchVariantMInterface $branchVariantRepository,
        protected CartDetailMInterface $cartDetailRepository,
    ) {}

    public function getCart(int $userId): ?Model
    {
        $this->userService->getById($userId);
        return $this->cartRepository->getCart($userId);

        throw new BadRequestException(
            'No se puede acceder al carrito sin usuario'
        );
    }

    public function getOrCreateCart(int $userId): Model
    {
        $this->userService->getById($userId);
        return $this->cartRepository->getOrCreateCart($userId);

        throw new BadRequestException(
            'No se puede crear el carrito sin usuario'
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

        if ($cartDetail) {
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
}
