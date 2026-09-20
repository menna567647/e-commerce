@extends('layouts.store')

@section('content')
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Your orders</p>
        <h1 class="mt-2 text-3xl font-black text-slate-900">Recent purchases</h1>
    </div>

    @if ($orders->isEmpty())
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-600">
            You have no orders yet.
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-slate-500">Order #{{ $order->order_number }}</p>
                            <p class="text-xl font-bold text-slate-900">${{ number_format($order->total, 2) }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.15em] text-slate-700">{{ $order->status }}</span>
                            <a href="{{ route('orders.show', $order->id) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">View details</a>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-slate-600">
                        Placed on {{ $order->created_at->format('M d, Y') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
