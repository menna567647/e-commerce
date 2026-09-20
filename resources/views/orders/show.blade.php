@extends('layouts.store')

@section('content')
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Order details</p>
        <h1 class="mt-2 text-3xl font-black text-slate-900">#{{ $order->order_number }}</h1>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-4">
            @foreach ($order->items as $item)
                <div class="flex items-center gap-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                    <img src="{{ $item->product->image ?: 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $item->product->name }}" class="h-20 w-20 rounded-2xl object-cover">
                    <div class="flex-1">
                        <p class="text-lg font-bold text-slate-900">{{ $item->product->name }}</p>
                        <p class="text-sm text-slate-600">Qty: {{ $item->quantity }}</p>
                    </div>
                    <p class="text-lg font-black text-slate-900">${{ number_format($item->total, 2) }}</p>
                </div>
            @endforeach
        </div>

        <aside class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <p class="text-sm text-slate-500">Status</p>
                <span class="mt-2 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.15em] text-slate-700">{{ $order->status }}</span>
            </div>

            <div>
                <p class="text-sm text-slate-500">Payment</p>
                <p class="mt-2 text-lg font-bold text-slate-900">{{ ucfirst($order->payment_method) }}</p>
            </div>

            <div class="space-y-2 text-sm text-slate-600">
                <div class="flex items-center justify-between"><span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex items-center justify-between"><span>Discount</span><span>${{ number_format($order->discount_amount, 2) }}</span></div>
                <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-base font-bold text-slate-900"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
            </div>
        </aside>
    </div>
@endsection
