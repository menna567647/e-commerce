<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $items = collect(session('cart', []))->values()->map(function ($item) {
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

        return view('cart.index', compact('items', 'subtotal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = max(1, (int) ($request->quantity ?? 1));

        if ($product->stock < $quantity) {
            return back()->with('error', 'Only '.$product->stock.' unit(s) of '.$product->name.' are available.');
        }

        $cart = session()->get('cart', []);
        $key = (string) $product->id;
        $newQuantity = ($cart[$key]['quantity'] ?? 0) + $quantity;

        if ($newQuantity > $product->stock) {
            return back()->with('error', 'You can only add '.$product->stock.' unit(s) of '.$product->name.' to the cart.');
        }

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $newQuantity;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', $product->name.' added to cart.');
    }

    public function update(Request $request, $productId): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $product = Product::find($productId);

            if (! $product) {
                return back()->with('error', 'This product is no longer available.');
            }

            $requestedQuantity = max(1, (int) $request->quantity);

            if ($requestedQuantity > $product->stock) {
                return back()->with('error', 'Only '.$product->stock.' unit(s) of '.$product->name.' are available.');
            }

            $cart[$productId]['quantity'] = $requestedQuantity;
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy($productId): RedirectResponse
    {
        $cart = session()->get('cart', []);

        unset($cart[$productId]);
        session()->put('cart', $cart);

        return back()->with('success', 'Item removed from cart.');
    }
}
