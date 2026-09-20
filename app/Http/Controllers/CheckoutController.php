<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Payments\PaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(protected PaymentGateway $paymentGateway)
    {
    }

    public function index(): View|RedirectResponse
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('info', 'Your cart is empty.');
        }

        $items = collect($cart)->map(function ($item) {
            $product = Product::find($item['product_id']);

            if (! $product) {
                return null;
            }

            return [
                'product' => $product,
                'quantity' => (int) $item['quantity'],
                'line_total' => $product->price * (int) $item['quantity'],
            ];
        })->filter()->values();

        $subtotal = $items->sum('line_total');

        return view('checkout.index', compact('items', 'subtotal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_email' => ['required', 'email'],
            'shipping_phone' => ['required', 'string', 'max:50'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['required', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'max:100'],
            'payment_method' => ['required', 'in:card,cash_on_delivery'],
            'coupon_code' => ['nullable', 'string'],
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }

        $items = collect($cart)->map(function ($item) {
            $product = Product::find($item['product_id']);

            if (! $product) {
                return null;
            }

            return [
                'product' => $product,
                'quantity' => (int) $item['quantity'],
                'line_total' => $product->price * (int) $item['quantity'],
            ];
        })->filter()->values();

        if ($items->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }

        foreach ($items as $item) {
            $product = $item['product'];

            if ($item['quantity'] > $product->stock) {
                return back()->withInput()->with('error', 'Only '.$product->stock.' unit(s) of '.$product->name.' are available.');
            }
        }

        $subtotal = $items->sum('line_total');
        $discountAmount = 0;
        $couponCode = null;

        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

            if ($coupon && $coupon->isValidFor($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
                $couponCode = $coupon->code;
            }
        }

        $total = max(0, $subtotal - $discountAmount);
        $paymentResult = $this->paymentGateway->charge([
            'amount' => $total,
            'currency' => 'USD',
            'payment_method' => $request->payment_method,
            'reference' => 'ORDER_'.uniqid(),
        ]);

        if (! $paymentResult['success']) {
            return back()->withInput()->with('error', $paymentResult['message'] ?? 'Payment failed. Please try again.');
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ORD-'.strtoupper(uniqid()),
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method,
            'shipping_name' => $request->shipping_name,
            'shipping_email' => $request->shipping_email,
            'shipping_phone' => $request->shipping_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_postal_code' => $request->shipping_postal_code,
            'shipping_country' => $request->shipping_country,
            'notes' => $request->notes,
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

        session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('success', 'Your order has been placed successfully.');
    }
}
