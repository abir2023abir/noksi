@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Products</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $products->total() }} products</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add product</a>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="adm-input max-w-xs">
    <select name="category_id" class="adm-select max-w-[180px]">
        <option value="">All categories</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
        @endforeach
    </select>
    <select name="brand_id" class="adm-select max-w-[160px]">
        <option value="">All brands</option>
        @foreach($brands as $b)
            <option value="{{ $b->id }}" @selected(request('brand_id') == $b->id)>{{ $b->name }}</option>
        @endforeach
    </select>
    <select name="status" class="adm-select max-w-[140px]">
        <option value="">Any status</option>
        <option value="active" @selected(request('status')==='active')>Active</option>
        <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
    </select>
    <select name="stock" class="adm-select max-w-[140px]">
        <option value="">Any stock</option>
        <option value="low" @selected(request('stock')==='low')>Low stock</option>
        <option value="out" @selected(request('stock')==='out')>Out of stock</option>
    </select>
    <button class="btn btn-secondary">Filter</button>
</form>

<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead>
            <tr>
                <th></th>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td><img src="{{ $product->image_url }}" alt="" class="h-12 w-10 rounded-lg object-cover bg-paper-100"></td>
                    <td>
                        <a href="{{ route('admin.products.show', $product) }}" class="font-semibold hover:text-plum-700">{{ $product->name }}</a>
                        <p class="text-xs text-ink-300">{{ $product->sku }}</p>
                    </td>
                    <td class="text-sm">{{ $product->category?->name ?? '—' }}</td>
                    <td class="font-semibold">{{ money($product->price) }}</td>
                    <td class="{{ $product->stock <= 5 ? 'text-amber-600 font-semibold' : '' }}">{{ $product->stock }}</td>
                    <td>
                        <span class="pill {{ status_badge($product->is_active ? 'active' : 'inactive') }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Delete this product?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-10 text-center text-ink-300">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
