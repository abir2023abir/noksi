@extends('admin.layouts.app')
@section('title', 'Stock movements')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Stock movements</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $movements->total() }} records</p>
    </div>
    <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">Inventory</a>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="number" name="product_id" value="{{ request('product_id') }}" placeholder="Product ID" class="adm-input max-w-[140px]">
    <button class="btn btn-secondary">Filter</button>
</form>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Date</th><th>Product</th><th>Variant</th><th>Qty</th><th>Type</th><th>User</th><th>Note</th></tr></thead>
        <tbody>
            @forelse($movements as $m)
                <tr>
                    <td class="text-sm whitespace-nowrap">{{ $m->created_at?->format('d M Y H:i') }}</td>
                    <td class="font-semibold">{{ $m->product?->name ?? '—' }}</td>
                    <td class="text-sm">{{ $m->variant ? trim(($m->variant->size ?? '').'/'.($m->variant->color ?? ''), '/') : '—' }}</td>
                    <td class="font-semibold {{ $m->quantity < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $m->quantity > 0 ? '+'.$m->quantity : $m->quantity }}</td>
                    <td class="capitalize text-sm">{{ $m->type }}</td>
                    <td class="text-sm">{{ $m->user?->name ?? '—' }}</td>
                    <td class="text-sm text-ink-500 max-w-xs truncate">{{ $m->note ?: ($m->reference ?: '—') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-10 text-center text-ink-300">No movements.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $movements->links() }}</div>
@endsection
