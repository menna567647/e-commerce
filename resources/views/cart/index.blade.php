@extends('layouts.store')

@section('content')
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Your cart</p>
        <h1 class="mt-2 text-3xl font-black text-slate-900">Shopping basket</h1>
    </div>

    @if ($items->isEmpty())
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
            <p class="text-lg font-semibold text-slate-700">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="mt-5 inline-flex rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">Continue shopping</a>
        </div>
    @else
        <div class="grid gap-8 lg:grid-cols-[1.5fr_0.7fr]">
            <div class="space-y-4">
                @foreach ($items as $item)
                    <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center">
                        <img src="{{ $item['product']->image ?: 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $item['product']->name }}" class="h-28 w-full rounded-2xl object-cover sm:w-28">

                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-4">
                                <h2 class="text-xl font-bold text-slate-900">{{ $item['product']->name }}</h2>
                                <form action="{{ route('cart.destroy', $item['product']->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-700">Remove</button>
                                </form>
                            </div>
                            <p class="mt-2 text-sm text-slate-600">{{ $item['product']->short_description ?? Str::limit($item['product']->description, 90) }}</p>
                            <div class="mt-4 flex items-center justify-between gap-4">
                                <form action="{{ route('cart.update', $item['product']->id) }}" method="POST" class="flex items-center gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <label class="text-sm font-medium text-slate-700">Qty</label>
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-20 rounded-full border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-indigo-500">
                                    <button type="submit" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-slate-400">Update</button>
                                </form>
                                <p class="text-xl font-black text-slate-900">${{ number_format($item['line_total'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black text-slate-900">Order summary</h2>
                <div class="mt-6 space-y-3 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                </div>
                <div class="mt-6 border-t border-slate-200 pt-4">
                    <div class="flex items-center justify-between text-lg font-black text-slate-900">
                        <span>Total</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                </div>
                <a href="{{ route('checkout.index') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">Proceed to checkout</a>
            </aside>
        </div>
    @endif
@endsection
