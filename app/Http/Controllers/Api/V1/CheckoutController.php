<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(protected CheckoutService $checkoutService)
    {
    }

    public function store(CheckoutRequest $request): JsonResponse
    {
        try {
            $order = $this->checkoutService->createOrderForCurrentUser($request->validated(), auth()->id());

            return response()->json([
                'message' => 'Order placed successfully.',
                'data' => new OrderResource($order),
            ], 201);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
