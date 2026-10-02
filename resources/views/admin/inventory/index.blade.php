@extends('admin.layouts.app')
@section('title', 'Inventory')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Inventory</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $products->total() }} tracked products</p>
    </div>
    <a href="{{ route('admin.inventory.movements') }}" class="btn btn-secondary">Movements</a>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="adm-input max-w-xs">
    <select name="stock" class="adm-select max-w-[140px]">
        <option value="">Any stock</option>
        <option value="low" @selected(request('stock')==='low')>Low stock</option>
        <option value="out" @selected(request('stock')==='out')>Out of stock</option>
    </select>
    <button class="btn btn-secondary">Filter</button>
</form>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 adm-card overflow-x-auto">
        <table class="adm-table">
            <thead><tr><th></th><th>Product</th><th>Category</th><th>Stock</th><th>Variants</th></tr></thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td><img src="{{ $product->image_url }}" class="h-12 w-10 rounded-lg object-cover bg-paper-100" alt=""></td>
                        <td>
                            <a href="{{ route('admin.products.show', $product) }}" class="font-semibold hover:text-plum-700">{{ $product->name }}</a>
                            <p class="text-xs text-ink-300">{{ $product->sku }}</p>
                        </td>
                        <td class="text-sm">{{ $product->category?->name ?? '—' }}</td>
                        <td class="{{ $product->stock <= 5 ? 'text-amber-600 font-semibold' : '' }}">{{ $product->stock }}</td>
                        <td class="text-xs text-ink-300">
                            @foreach($product->variants->take(4) as $v)
                                <div>{{ $v->size }}{{ $v->color ? '/'.$v->color : '' }}: {{ $v->stock }}</div>
                            @endforeach
                            @if($product->variants->count() > 4)…@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-ink-300">No products.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form method="POST" action="{{ route('admin.inventory.adjust') }}" class="adm-card p-6 space-y-4 h-fit">
        @csrf
        <h2 class="adm-card-title">Adjust stock</h2>
        <div>
            <label class="adm-label">Product *</label>
            <select name="product_id" class="adm-select" required>
                <option value="">Select…</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->stock }})</option>
                @endforeach
            </select>
            @error('product_id')<p class="adm-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="adm-label">Variant ID (optional)</label>
            <input type="number" name="product_variant_id" class="adm-input" placeholder="Leave blank for product">
        </div>
        <div>
            <label class="adm-label">Quantity *</label>
            <input type="number" name="quantity" class="adm-input" required placeholder="+10 or -3">
            <p class="adm-hint">Positive to add, negative to remove</p>
            @error('quantity')<p class="adm-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="adm-label">Type *</label>
            <select name="type" class="adm-select" required>
                @foreach(['adjustment','restock','damage','return','sale'] as $t)
                    <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="adm-label">Reference</label>
            <input type="text" name="reference" class="adm-input">
        </div>
        <div>
            <label class="adm-label">Note</label>
            <textarea name="note" rows="2" class="adm-input"></textarea>
        </div>
        <button class="btn btn-primary w-full">Apply adjustment</button>
    </form>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
