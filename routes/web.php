<?php

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('auth')->post('/cart/add', function (Request $request, CartService $cartService) {
    try {
        $cartService->addProduct((int) $request->product_id, (int) ($request->quantity ?? 1));

        return redirect()->back();
    } catch (\RuntimeException $e) {
        session()->flash('error', $e->getMessage());

        return redirect()->back();
    }
})->name('cart.add');

require __DIR__.'/auth.php';
