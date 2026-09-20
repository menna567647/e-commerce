@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl p-6">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Admin Dashboard</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Store analytics</h1>
            </div>
            <a href="{{ route('admin.products.create') }}" class="rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">Add product</a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Products</p>
                <p class="mt-4 text-3xl font-black text-slate-900">{{ $stats['products'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Orders</p>
                <p class="mt-4 text-3xl font-black text-slate-900">{{ $stats['orders'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Revenue</p>
                <p class="mt-4 text-3xl font-black text-slate-900">${{ number_format($stats['revenue'], 2) }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Pending</p>
                <p class="mt-4 text-3xl font-black text-slate-900">{{ $stats['pending'] }}</p>
            </div>
        </div>

        <div class="mt-10 grid gap-8 xl:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black text-slate-900">Recent orders</h2>
                <div class="mt-6 space-y-4">
                    @foreach ($recentOrders as $order)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $order->order_number }}</p>
                                <p class="text-sm text-slate-500">{{ $order->user?->name ?? 'Guest' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-slate-900">${{ number_format($order->total, 2) }}</p>
                                <p class="text-xs uppercase tracking-[0.15em] text-slate-500">{{ $order->status }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black text-slate-900">Low stock alert</h2>
                <div class="mt-6 space-y-4">
                    @foreach ($lowStockProducts as $product)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                <p class="text-sm text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            </div>
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $product->stock }} left</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
