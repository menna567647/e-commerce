@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl p-6">
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Sales</p>
            <h1 class="mt-2 text-3xl font-black text-slate-900">Order management</h1>
        </div>

        <div class="space-y-4">
            @foreach ($orders as $order)
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Order #{{ $order->order_number }}</p>
                            <p class="mt-1 text-xl font-bold text-slate-900">{{ $order->user?->name ?? 'Guest customer' }}</p>
                        </div>
                        <div class="flex gap-3 text-sm text-slate-600">
                            <span>Total: <strong class="text-slate-900">${{ number_format($order->total, 2) }}</strong></span>
                            <span>Payment: <strong class="text-slate-900">{{ ucfirst($order->payment_method) }}</strong></span>
                        </div>
                        <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="flex items-center gap-3">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="rounded-full border border-slate-300 bg-slate-50 px-4 py-2 text-sm outline-none focus:border-indigo-500">
                                @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Update</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
