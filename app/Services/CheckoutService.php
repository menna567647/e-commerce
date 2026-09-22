<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\Payments\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService,
        protected PaymentGateway $paymentGateway,
    ) {
    }

    public function createOrderForCurrentUser(array $data, int $userId): Order
    {
        $user = User::findOrFail($userId);
        $items = $this->cartService->getCartItemsForUser($user);

        if ($items->isEmpty()) {
            $legacyItems = $this->cartService->resolveCartItems(session('cart', []));

            if ($legacyItems->isEmpty()) {
                throw new \RuntimeException('Your cart is empty.');
            }

            $items = $legacyItems;
        }

        foreach ($items as $item) {
            $product = $item['product'];
            $this->cartService->ensureStockAvailable($product, $item['quantity']);
        }

        $subtotal = (float) $items->sum('line_total');
        $discountAmount = $this->couponService->calculateDiscount($data['coupon_code'] ?? null, $subtotal);
        $total = max(0, $subtotal - $discountAmount);

        $paymentResult = $this->paymentGateway->charge([
            'amount' => $total,
            'currency' => 'USD',
            'payment_method' => $data['payment_method'],
            'reference' => 'ORDER_'.uniqid(),
        ]);

        if (! $paymentResult['success']) {
            throw new \RuntimeException($paymentResult['message'] ?? 'Payment failed. Please try again.');
        }

        return DB::transaction(function () use ($data, $items, $subtotal, $discountAmount, $total, $user) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-'.strtoupper(Str::uuid()->toString()),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'paid',
                'payment_method' => $data['payment_method'],
                'shipping_name' => $data['shipping_name'],
                'shipping_email' => $data['shipping_email'],
                'shipping_phone' => $data['shipping_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'shipping_state' => $data['shipping_state'],
                'shipping_postal_code' => $data['shipping_postal_code'],
                'shipping_country' => $data['shipping_country'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $product = $item['product'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $item['line_total'],
                ]);

                $product->stock = max(0, $product->stock - $item['quantity']);
                $product->save();
            }

            $user->cartItems()->delete();
            session()->forget('cart');

            return $order->load('items.product');
        });
    }
}
