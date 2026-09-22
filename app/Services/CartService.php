<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getPayload(): array
    {
        $items = $this->getCartItemsForCurrentUser();
        $subtotal = (float) $items->sum('line_total');

        return [
            'items' => $items->all(),
            'subtotal' => round($subtotal, 2),
            'discount' => 0.0,
            'total' => round($subtotal, 2),
        ];
    }

    public function getCartItemsForCurrentUser(): Collection
    {
        $user = Auth::user();

        if ($user) {
            $items = $this->getCartItemsForUser($user);

            if ($items->isNotEmpty()) {
                return $items;
            }

            $legacyItems = $this->resolveCartItems(session('cart', []));

            if ($legacyItems->isNotEmpty()) {
                return $legacyItems;
            }

            return $items;
        }

        return $this->resolveCartItems(session('cart', []));
    }

    public function getCartItemsForUser(User $user): Collection
    {
        return $user->cartItems()->with('product')->get()->map(function (Cart $item) {
            $product = $item->product;

            if (! $product || ! $product->is_active) {
                return null;
            }

            $quantity = max(1, (int) $item->quantity);

            return [
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => round((float) $product->price * $quantity, 2),
            ];
        })->filter()->values();
    }

    public function resolveCartItems(array $cart = []): Collection
    {
        return collect($cart)->map(function ($item) {
            $product = Product::find($item['product_id'] ?? null);

            if (! $product || ! $product->is_active) {
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
        $product = Product::find($productId);

        if (! $product || ! $product->is_active) {
            throw new \RuntimeException('This product is no longer available.');
        }

        $requestedQuantity = max(1, (int) $quantity);

        $user = Auth::user();

        if ($user) {
            $cartItem = $user->cartItems()->where('product_id', $productId)->first();
            $newQuantity = ($cartItem ? (int) $cartItem->quantity : 0) + $requestedQuantity;

            $this->ensureStockAvailable($product, $newQuantity);

            if ($cartItem) {
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                $user->cartItems()->create([
                    'product_id' => $productId,
                    'quantity' => $requestedQuantity,
                ]);
            }

            return $this->getPayload();
        }

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
        $requestedQuantity = max(1, (int) $quantity);
        $user = Auth::user();

        if ($user) {
            $cartItem = $user->cartItems()->where('product_id', $productId)->first();

            if (! $cartItem) {
                throw new \RuntimeException('Product not found in cart.');
            }

            $product = $cartItem->product;

            if (! $product || ! $product->is_active) {
                throw new \RuntimeException('This product is no longer available.');
            }

            $this->ensureStockAvailable($product, $requestedQuantity);
            $cartItem->quantity = $requestedQuantity;
            $cartItem->save();

            return $this->getPayload();
        }

        $cart = session()->get('cart', []);

        if (! isset($cart[$productId])) {
            throw new \RuntimeException('Product not found in cart.');
        }

        $product = Product::find($productId);

        if (! $product || ! $product->is_active) {
            throw new \RuntimeException('This product is no longer available.');
        }

        $this->ensureStockAvailable($product, $requestedQuantity);

        $cart[$productId]['quantity'] = $requestedQuantity;
        session()->put('cart', $cart);

        return $this->getPayload();
    }

    public function removeProduct(int $productId): array
    {
        $user = Auth::user();

        if ($user) {
            $user->cartItems()->where('product_id', $productId)->delete();

            return $this->getPayload();
        }

        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);

        return $this->getPayload();
    }

    public function clearCart(): array
    {
        $user = Auth::user();

        if ($user) {
            $user->cartItems()->delete();

            return [
                'items' => [],
                'subtotal' => 0.0,
                'discount' => 0.0,
                'total' => 0.0,
            ];
        }

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
        if (! $product->is_active) {
            throw new \RuntimeException('This product is no longer available.');
        }

        if ($quantity > $product->stock) {
            throw new \RuntimeException('Only '.$product->stock.' item(s) of '.$product->name.' are available.');
        }
    }
}
