<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

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

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load('category');
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
