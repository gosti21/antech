<?php

namespace App\Http\Controllers\Api\v1\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Shop\Cart\AddCartSRequest;
use App\Http\Requests\Api\v1\Shop\Cart\MergeCartSRequest;
use App\Http\Requests\Api\v1\Shop\Cart\UpdateCartSRequest;
use App\Http\Resources\Api\v1\Shop\Cart\CartDetailSResource;
use App\Http\Resources\Api\v1\Shop\Cart\CartSResource;
use App\Services\Api\v1\Shop\CartDetailSService;
use App\Services\Api\v1\Shop\CartSService;
use App\Services\Api\v1\Shop\UserSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartSController extends Controller
{
    public function __construct(
        protected CartSService $cartService,
        protected CartDetailSService $cartDetailService,
        protected UserSService $userService,
    ) {}

    public function getCart(Request $request): JsonResponse
    {
        $userId = Auth::check() ? Auth::id() : null;
        $sessionId = $request->header('X-Session-ID');

        $cart = $this->cartService->getCart($userId, $sessionId);
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
                'cart' => new CartSResource($cart),
                'totals' => $cart->calculateTotals()
            ]
        ]);
    }

    public function addItem(AddCartSRequest $request): JsonResponse
    {
        $requestValidate = $request->validated();
        try{
            $userId = Auth::check() ? Auth::id() : null;
            $sessionId = $request->header('X-Session-ID');

            $cart = $this->cartService->getOrCreateCart($userId, $sessionId);
            $cartDetail = $this->cartService->addItemsToCart(
                $cart,
                $requestValidate['branch_variant_id'],
                $requestValidate['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'data' => [
                    'item' => new CartDetailSResource($cartDetail),
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
        $sessionId = $request->header('X-Session-ID');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionId);
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
                'item' => new CartDetailSResource($cartDetail),
                'totals' => $cart->fresh()->calculateTotals()
            ]
        ]);
    }

    public function removeItem(int $branchVariantId, Request $request): JsonResponse
    {
        $userId = Auth::check() ? Auth::id() : null;
        $sessionId = $request->header('X-Session-ID');

        $cart = $this->cartService->getOrCreateCart($userId, $sessionId);
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

    public function deleteCart(Request $request): JsonResponse
    {
        try {
            $userId = Auth::check() ? Auth::id() : null;
            $sessionId = $request->header('X-Session-ID');

            $cart = $this->cartService->getOrCreateCart($userId, $sessionId);
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

    public function merge(MergeCartSRequest $request): JsonResponse
    {
        $requestValidate = $request->validated();
        $userId = Auth::check() ? Auth::id() : 0;
        $this->userService->getById($userId);
        try {
            $cart = $this->cartService->mergeGuestCart(
                $requestValidate['session_id'],
                $userId
            );
            return response()->json([
                'success' => true,
                'message' => 'Carrito sincronizado exitosamente',
                'data' => [
                    'cart' => new CartSResource($cart),
                    'totals' => $cart->calculateTotals()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al sincronizar carrito',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
