@extends('layouts.store')

@section('content')
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Checkout</p>
        <h1 class="mt-2 text-3xl font-black text-slate-900">Complete your order</h1>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
        <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Full name</label>
                    <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()?->name ?? '') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" name="shipping_email" value="{{ old('shipping_email', auth()->user()?->email ?? '') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Phone</label>
                    <input type="text" name="shipping_phone" value="{{ old('shipping_phone') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Country</label>
                    <input type="text" name="shipping_country" value="{{ old('shipping_country', 'United States') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Street address</label>
                <input type="text" name="shipping_address" value="{{ old('shipping_address') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">City</label>
                    <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">State</label>
                    <input type="text" name="shipping_state" value="{{ old('shipping_state') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Zip code</label>
                    <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Order notes (optional)</label>
                <textarea name="notes" rows="3" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500">{{ old('notes') }}</textarea>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Payment method</label>
                <select name="payment_method" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500">
                    <option value="card">Mock Card Payment</option>
                    <option value="cash_on_delivery">Cash on delivery</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Coupon code</label>
                <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="e.g. SAVE10" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500">
            </div>

            <button type="submit" class="w-full rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-700">Place order</button>
        </form>

        <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black text-slate-900">Order summary</h2>
            <div class="mt-6 space-y-4">
                @foreach ($items as $item)
                    <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3">
                        <img src="{{ $item['product']->image ?: 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $item['product']->name }}" class="h-16 w-16 rounded-xl object-cover">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-900">{{ $item['product']->name }}</p>
                            <p class="text-xs text-slate-600">Qty: {{ $item['quantity'] }}</p>
                        </div>
                        <p class="text-sm font-bold text-slate-900">${{ number_format($item['line_total'], 2) }}</p>
                    </div>
                @endforeach
            </div>

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
        </aside>
    </div>
@endsection
