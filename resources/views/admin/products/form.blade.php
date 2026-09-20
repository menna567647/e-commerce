@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl p-6">
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Product</p>
            <h1 class="mt-2 text-3xl font-black text-slate-900">{{ $product ? 'Edit product' : 'Create product' }}</h1>
        </div>

        <form method="POST" action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($product)
                @method('PUT')
            @endif

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Category</label>
                    <select name="category_id" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product?->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Product name</label>
                <input type="text" name="name" value="{{ old('name', $product?->name) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Short description</label>
                <input type="text" name="short_description" value="{{ old('short_description', $product?->short_description) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                <textarea name="description" rows="5" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>{{ old('description', $product?->description) }}</textarea>
            </div>

            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Price</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product?->price) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Compare price</label>
                    <input type="number" step="0.01" min="0" name="compare_price" value="{{ old('compare_price', $product?->compare_price) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Stock</label>
                    <input type="number" min="0" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500" required>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Product image</label>
                    <input type="file" name="image" accept="image/*" class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white">
                </div>
                <div class="flex items-center gap-5 pt-8">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product?->is_featured) ? 'checked' : '' }}>
                        Featured
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.products') }}" class="rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:border-slate-400">Cancel</a>
                <button type="submit" class="rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">{{ $product ? 'Update product' : 'Save product' }}</button>
            </div>
        </form>
    </div>
@endsection
