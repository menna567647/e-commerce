@extends('layouts.store')

@section('content')
    <section class="rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 px-6 py-16 text-white shadow-xl sm:px-10">
        <div class="grid gap-10 lg:grid-cols-[1.3fr_0.7fr] lg:items-center">
            <div>
                <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] text-indigo-100">Fresh arrivals</span>
                <h1 class="mt-6 text-4xl font-black tracking-tight sm:text-5xl">Upgrade your everyday essentials.</h1>
                <p class="mt-5 max-w-xl text-base text-slate-200 sm:text-lg">Discover premium electronics, elevated fashion, and home essentials designed for modern living.</p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900">Shop now</a>
                    <a href="{{ route('home') }}#categories" class="rounded-full border border-white/30 px-6 py-3 text-sm font-semibold text-white">Browse categories</a>
                </div>
            </div>

            <div class="rounded-3xl bg-white/10 p-4 shadow-2xl backdrop-blur-sm">
                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=80" alt="Featured products" class="h-[420px] w-full rounded-2xl object-cover">
            </div>
        </div>
    </section>

    <section id="categories" class="mt-14">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Popular categories</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900">Shop the latest trends</h2>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <img src="{{ $category->image ?: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $category->name }}" class="h-48 w-full object-cover group-hover:scale-105 transition duration-300">
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="text-lg font-bold text-slate-900">{{ $category->name }}</h3>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-700">{{ $category->products_count }} items</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-600">{{ Str::limit($category->description ?? 'Browse our handpicked essentials.', 80) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mt-14">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Featured picks</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900">Top sellers this week</h2>
            </div>
            <a href="{{ route('products.index') }}" class="hidden text-sm font-semibold text-indigo-700 hover:text-indigo-900 sm:inline-flex">View all products →</a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($featuredProducts as $product)
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <a href="{{ route('products.show', $product->slug) }}" class="block">
                        <img src="{{ $product->image ?: 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $product->name }}" class="h-60 w-full object-cover">
                    </a>
                    <div class="p-5">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ $product->category->name }}</span>
                            @if ($product->compare_price)
                                <span class="text-xs font-medium text-slate-500 line-through">${{ number_format($product->compare_price, 2) }}</span>
                            @endif
                        </div>
                        <a href="{{ route('products.show', $product->slug) }}" class="text-xl font-bold text-slate-900 hover:text-indigo-700">{{ $product->name }}</a>
                        <p class="mt-2 text-sm text-slate-600">{{ Str::limit($product->short_description ?? $product->description, 90) }}</p>
                        <div class="mt-5 flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-black text-slate-900">${{ number_format($product->price, 2) }}</p>
                            </div>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Add to cart</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
