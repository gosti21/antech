<?php

namespace App\Http\Controllers\Api\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Shop\Cart\AddCartSRequest;
use App\Http\Requests\Api\v1\Shop\Cart\UpdateCartSRequest;
use App\Http\Resources\Api\v1\Mobile\CartDetailMResource;
use App\Http\Resources\Api\v1\Mobile\CartMResource;
use App\Services\Api\v1\Mobile\CartDetailMService;
use App\Services\Api\v1\Mobile\CartMService;
use App\Services\Api\v1\Mobile\UserMService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartMController extends Controller
{
    public function __construct(
        protected CartMService $cartService,
        protected CartDetailMService $cartDetailService,
        protected UserMService $userService,
    ) {}

    public function getCart(): JsonResponse
    {
        $userId = Auth::check() ? Auth::id() : null;

        $cart = $this->cartService->getCart($userId);
        if (!$cart) {
            return response()->json([
                'success' => true,
                'message' => 'Carrito vacío',
                'data' => [
                    'cart' => [],
                    'totals' => [
                        'items' => 0,
                        'total' => 0
                    ]
                ]
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Carrito obtenido exitosamente',
            'data' => [
                'cart' => new CartMResource($cart),
                'totals' => $cart->calculateTotals()
            ]
        ]);
    }

    public function addItem(AddCartSRequest $request): JsonResponse
    {
        $requestValidate = $request->validated();
        try {
            $userId = Auth::check() ? Auth::id() : null;

            $cart = $this->cartService->getOrCreateCart($userId);
            $cartDetail = $this->cartService->addItemsToCart(
                $cart,
                $requestValidate['branch_variant_id'],
                $requestValidate['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'data' => [
                    'item' => new CartDetailMResource($cartDetail),
                    'totals' => $cart->fresh()->calculateTotals()
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar producto',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function updateItem(int $branchVariantId, UpdateCartSRequest $request): JsonResponse
    {
        $requestValidate = $request->validated();
        $userId = Auth::check() ? Auth::id() : null;

        $cart = $this->cartService->getOrCreateCart($userId);
        $this->cartDetailService->getById($cart->id, $branchVariantId);

        if ($requestValidate['quantity'] === 0) {
            $this->cartService->removeItem($cart->id, $branchVariantId);

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'data' => [
                    'totals' => $cart->fresh()->calculateTotals()
                ]
            ]);
        }

        $cartDetail = $this->cartService->updateItemQuantity(
            $cart->id,
            $branchVariantId,
            $requestValidate['quantity']
        );

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada',
            'data' => [
                'item' => new CartDetailMResource($cartDetail),
                'totals' => $cart->fresh()->calculateTotals()
            ]
        ]);
    }

    public function removeItem(int $branchVariantId): JsonResponse
    {
        $userId = Auth::check() ? Auth::id() : null;

        $cart = $this->cartService->getOrCreateCart($userId);
        $this->cartDetailService->getById($cart->id, $branchVariantId);
        $this->cartService->removeItem($cart->id, $branchVariantId);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito',
            'data' => [
                'totals' => $cart->fresh()->calculateTotals()
            ]
        ]);
    }

    public function deleteCart(): JsonResponse
    {
        try {
            $userId = Auth::check() ? Auth::id() : null;

            $cart = $this->cartService->getOrCreateCart($userId);
            $this->cartService->deleteCart($cart->id);

            return response()->json([
                'success' => true,
                'message' => 'Carrito vaciado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al vaciar el carrito',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
