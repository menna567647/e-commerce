<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    public function getPayload(): array
    {
        $items = $this->resolveCartItems(session('cart', []));
        $subtotal = (float) $items->sum('line_total');

        return [
            'items' => $items->all(),
            'subtotal' => round($subtotal, 2),
            'discount' => 0.0,
            'total' => round($subtotal, 2),
        ];
    }

    public function resolveCartItems(array $cart = []): Collection
    {
        return collect($cart)->map(function ($item) {
            $product = Product::find($item['product_id'] ?? null);

            if (! $product) {
                return null;
            }

            $quantity = max(0, (int) ($item['quantity'] ?? 0));

            return [
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => round((float) $product->price * $quantity, 2),
            ];
        })->filter()->values();
    }

    public function addProduct(int $productId, int $quantity): array
    {
        $product = Product::findOrFail($productId);
        $requestedQuantity = max(1, (int) $quantity);
        $cart = session()->get('cart', []);
        $key = (string) $product->id;
        $currentQuantity = (int) ($cart[$key]['quantity'] ?? 0);
        $newQuantity = $currentQuantity + $requestedQuantity;

        $this->ensureStockAvailable($product, $newQuantity);

        $cart[$key] = [
            'product_id' => $product->id,
            'quantity' => $newQuantity,
        ];

        session()->put('cart', $cart);

        return $this->getPayload();
    }

    public function updateProductQuantity(int $productId, int $quantity): array
    {
        $cart = session()->get('cart', []);

        if (! isset($cart[$productId])) {
            throw new \RuntimeException('Product not found in cart.');
        }

        $product = Product::find($productId);

        if (! $product) {
            throw new \RuntimeException('This product is no longer available.');
        }

        $requestedQuantity = max(1, (int) $quantity);
        $this->ensureStockAvailable($product, $requestedQuantity);

        $cart[$productId]['quantity'] = $requestedQuantity;
        session()->put('cart', $cart);

        return $this->getPayload();
    }

    public function removeProduct(int $productId): array
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);

        return $this->getPayload();
    }

    public function clearCart(): array
    {
        session()->forget('cart');

        return [
            'items' => [],
            'subtotal' => 0.0,
            'discount' => 0.0,
            'total' => 0.0,
        ];
    }

    public function ensureStockAvailable(Product $product, int $quantity): void
    {
        if ($quantity > $product->stock) {
            throw new \RuntimeException('Only '.$product->stock.' item(s) of '.$product->name.' are available.');
        }
    }
}
