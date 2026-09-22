<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(protected CheckoutService $checkoutService)
    {
    }

    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => OrderResource::collection($orders->items())->resolve(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id() || Auth::user()?->is_admin, 403);

        $order->load('items.product');

        return response()->json([
            'data' => new OrderResource($order),
        ]);
    }

    public function checkout(CheckoutRequest $request)
    {
        try {
            $order = $this->checkoutService->createOrderForCurrentUser($request->validated(), Auth::id());

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

    public function cancel(Order $order)
    {
        abort_unless($order->user_id === Auth::id() || Auth::user()?->is_admin, 403);
        abort_if(in_array($order->status, ['cancelled', 'shipped', 'delivered'], true), 422, 'This order cannot be cancelled.');

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'message' => 'Order cancelled successfully.',
            'data' => new OrderResource($order->load('items.product')),
        ]);
    }
}
