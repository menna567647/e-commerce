<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::active()
            ->with('category')
            ->filter([
                'search' => $request->query('search'),
                'category' => $request->query('category'),
                'min_price' => $request->query('min_price'),
                'max_price' => $request->query('max_price'),
            ])
            ->when($request->query('sort') === 'price_asc', fn ($query) => $query->orderBy('price', 'asc'))
            ->when($request->query('sort') === 'price_desc', fn ($query) => $query->orderBy('price', 'desc'))
            ->when($request->query('sort') === 'newest', fn ($query) => $query->latest())
            ->when(! $request->filled('sort'), fn ($query) => $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'))
            ->paginate(12)
            ->withQueryString();

        return response()->json([
            'data' => ProductResource::collection($products->items())->resolve(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function featured()
    {
        $products = Product::active()
            ->featured()
            ->with('category')
            ->latest()
            ->get();

        return response()->json([
            'data' => ProductResource::collection($products)->resolve(),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('category');

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }
}
