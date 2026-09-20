<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->limit(4)->get();
        $featuredProducts = Product::active()->featured()->with('category')->take(8)->get();

        return view('home', compact('categories', 'featuredProducts'));
    }
}
