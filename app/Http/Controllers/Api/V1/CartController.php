<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AddToCartRequest;
use App\Http\Requests\Api\V1\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use RuntimeException;

class CartController extends Controller
{ 
    public function __construct(protected CartService $cartService)
    {
    }

    public function index()
    {
        return response()->json([
            'data' => new CartResource($this->cartService->getPayload()),
        ]);
    }

    public function store(AddToCartRequest $request)
    {
        try {
            $payload = $this->cartService->addProduct($request->product_id, (int) ($request->quantity ?? 1));

            return response()->json([
                'message' => 'Product added to cart.',
                'data' => new CartResource($payload),
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(UpdateCartRequest $request, int $productId)
    {
        try {
            $payload = $this->cartService->updateProductQuantity($productId, (int) $request->quantity);

            return response()->json([
                'message' => 'Cart updated successfully.',
                'data' => new CartResource($payload),
            ]);
        } catch (RuntimeException $e) {
            $status = str_contains($e->getMessage(), 'not found') || str_contains($e->getMessage(), 'no longer available') ? 404 : 422;

            return response()->json([
                'message' => $e->getMessage(),
            ], $status);
        }
    }

    public function destroy(int $productId)
    {
        $payload = $this->cartService->removeProduct($productId);

        return response()->json([
            'message' => 'Cart cleared successfully.',
            'data' => new CartResource($payload),
        ]);
    }

    public function clear()
    {
        return response()->json([
            'message' => 'Cart cleared successfully.',
            'data' => new CartResource($this->cartService->clearCart()),
        ]);
    }
}
