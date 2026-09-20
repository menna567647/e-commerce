@extends('layouts.store')

@section('content')
    <div class="grid gap-10 lg:grid-cols-2">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-3 shadow-sm">
            <img src="{{ $product->image ?: 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $product->name }}" class="h-full min-h-[520px] w-full rounded-2xl object-cover">
        </div>

        <div class="space-y-6">
            <div>
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-700">{{ $product->category->name }}</span>
                <h1 class="mt-4 text-4xl font-black text-slate-900">{{ $product->name }}</h1>
            </div>

            <div class="flex items-end gap-4">
                <p class="text-4xl font-black text-slate-900">${{ number_format($product->price, 2) }}</p>
                @if ($product->compare_price)
                    <p class="text-lg text-slate-500 line-through">${{ number_format($product->compare_price, 2) }}</p>
                    <p class="rounded-full bg-emerald-100 px-2.5 py-1 text-sm font-semibold text-emerald-700">Save {{ $product->discount_percent }}%</p>
                @endif
            </div>

            <p class="text-base leading-7 text-slate-600">{{ $product->description }}</p>

            <div class="flex items-center gap-3 rounded-2xl bg-slate-100 p-3 text-sm text-slate-700">
                <span class="font-semibold">Availability:</span>
                <span>{{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}</span>
            </div>

            <form action="{{ route('cart.add') }}" method="POST" class="flex flex-wrap items-center gap-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <label class="flex items-center gap-3 rounded-full border border-slate-300 bg-white px-4 py-3">
                    <span class="text-sm font-medium text-slate-600">Qty</span>
                    <input type="number" name="quantity" min="1" value="1" class="w-16 border-0 bg-transparent text-center text-lg font-semibold text-slate-900 outline-none">
                </label>
                <button type="submit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-700">Add to cart</button>
            </form>
        </div>
    </div>

    @if ($relatedProducts->isNotEmpty())
        <section class="mt-16">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">You may also like</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900">Related items</h2>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($relatedProducts as $related)
                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <a href="{{ route('products.show', $related->slug) }}">
                            <img src="{{ $related->image ?: 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $related->name }}" class="h-56 w-full object-cover">
                        </a>
                        <div class="p-4">
                            <a href="{{ route('products.show', $related->slug) }}" class="text-lg font-bold text-slate-900 hover:text-indigo-700">{{ $related->name }}</a>
                            <div class="mt-4 flex items-center justify-between">
                                <p class="text-xl font-black text-slate-900">${{ number_format($related->price, 2) }}</p>
                                <a href="{{ route('products.show', $related->slug) }}" class="rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold text-white">View</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection
