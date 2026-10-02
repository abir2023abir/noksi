@extends('admin.layouts.app')
@section('title', 'Edit flash sale')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Edit flash sale</h1><p class="mt-1 text-sm text-ink-500">{{ $flashSale->title }}</p></div>
    <div class="flex gap-2">
        <a href="{{ route('admin.flash-sales.show', $flashSale) }}" class="btn btn-secondary">View</a>
        <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-ghost">Back</a>
    </div>
</div>
<form method="POST" action="{{ route('admin.flash-sales.update', $flashSale) }}" enctype="multipart/form-data" class="adm-card max-w-2xl p-6 space-y-4 mb-6">
    @csrf @method('PUT')
    @include('admin.flash-sales._form', ['flashSale' => $flashSale])
    <div class="flex justify-end gap-2"><button class="btn btn-primary">Save</button></div>
</form>

<div class="grid gap-6 lg:grid-cols-2">
    <div class="adm-card overflow-x-auto">
        <div class="adm-card-head"><h2 class="adm-card-title">Products in sale</h2></div>
        <table class="adm-table">
            <thead><tr><th>Product</th><th>Sale price</th><th>Limit</th><th></th></tr></thead>
            <tbody>
                @forelse($flashSale->items as $item)
                    <tr>
                        <td class="font-semibold">{{ $item->product?->name ?? '—' }}</td>
                        <td>{{ money($item->sale_price) }}</td>
                        <td>{{ $item->quantity_limit ?? '∞' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.flash-sales.products.destroy', [$flashSale, $item]) }}" onsubmit="return confirm('Remove product?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm text-rose-600">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-8 text-center text-ink-300">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <form method="POST" action="{{ route('admin.flash-sales.products.store', $flashSale) }}" class="adm-card p-6 space-y-4 h-fit">
        @csrf
        <h2 class="adm-card-title">Add product</h2>
        <div>
            <label class="adm-label">Product *</label>
            <select name="product_id" class="adm-select" required>
                <option value="">Select…</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} ({{ money($p->price) }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="adm-label">Sale price *</label>
            <input type="number" step="0.01" min="0" name="sale_price" class="adm-input" required>
        </div>
        <div>
            <label class="adm-label">Quantity limit</label>
            <input type="number" min="1" name="quantity_limit" class="adm-input">
        </div>
        <button class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add</button>
    </form>
</div>
@endsection
