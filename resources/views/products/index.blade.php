@extends('layouts.store')

@section('content')
    <div class="mb-8 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Collection</p>
                <h1 class="mt-2 text-3xl font-black text-slate-900">Shop all products</h1>
            </div>

            <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products" class="w-full rounded-full border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none ring-0 focus:border-indigo-500 sm:w-64">
                <select name="category" class="rounded-full border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none focus:border-indigo-500">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="sort" class="rounded-full border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none focus:border-indigo-500">
                    <option value="">Featured</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                </select>
                <button type="submit" class="rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">Apply</button>
            </form>
        </div>
    </div>

    @if ($products->isEmpty())
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-slate-600">
            No products matched your search.
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <img src="{{ $product->image ?: 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $product->name }}" class="h-64 w-full object-cover">
                    </a>
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $product->category->name }}</span>
                            @if ($product->compare_price)
                                <span class="text-xs text-slate-500 line-through">${{ number_format($product->compare_price, 2) }}</span>
                            @endif
                        </div>
                        <a href="{{ route('products.show', $product->slug) }}" class="mt-4 block text-xl font-bold text-slate-900 hover:text-indigo-700">{{ $product->name }}</a>
                        <p class="mt-2 text-sm text-slate-600">{{ Str::limit($product->short_description ?? $product->description, 80) }}</p>
                        <div class="mt-5 flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-black text-slate-900">${{ number_format($product->price, 2) }}</p>
                                @if ($product->compare_price)
                                    <p class="text-xs font-medium text-emerald-600">Save {{ $product->discount_percent }}%</p>
                                @endif
                            </div>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Add</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>
    @endif
@endsection
