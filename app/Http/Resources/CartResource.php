<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request): array
    {
        $items = collect($this->resource['items'] ?? [])->map(function ($item) {
            $product = $item['product'] ?? null;

            return [
                'product' => $product instanceof JsonResource ? $product : ($product ? new ProductResource($product) : null),
                'quantity' => (int) ($item['quantity'] ?? 0),
                'line_total' => (float) ($item['line_total'] ?? 0),
            ];
        })->values()->all();

        return [
            'items' => $items,
            'subtotal' => (float) ($this->resource['subtotal'] ?? 0),
            'discount' => (float) ($this->resource['discount'] ?? 0),
            'total' => (float) ($this->resource['total'] ?? 0),
        ];
    }
}
