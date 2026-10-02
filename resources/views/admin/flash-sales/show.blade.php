@extends('admin.layouts.app')
@section('title', $flashSale->title)
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">{{ $flashSale->title }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $flashSale->starts_at?->format('d M Y H:i') }} – {{ $flashSale->ends_at?->format('d M Y H:i') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.flash-sales.edit', $flashSale) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
<div class="mb-4 flex flex-wrap gap-2">
    <span class="pill {{ status_badge($flashSale->is_active ? 'active' : 'inactive') }}">{{ $flashSale->is_active ? 'Active' : 'Inactive' }}</span>
    @if($flashSale->subtitle)<span class="text-sm text-ink-500">{{ $flashSale->subtitle }}</span>@endif
</div>
@if($flashSale->image)
    <img src="{{ img($flashSale->image) }}" alt="" class="mb-6 h-40 w-full max-w-xl rounded-xl object-cover">
@endif
<div class="adm-card overflow-x-auto">
    <div class="adm-card-head"><h2 class="adm-card-title">Products ({{ $flashSale->items->count() }})</h2></div>
    <table class="adm-table">
        <thead><tr><th></th><th>Product</th><th>Sale price</th><th>Limit</th><th>Sold</th><th></th></tr></thead>
        <tbody>
            @forelse($flashSale->items as $item)
                <tr>
                    <td>@if($item->product?->primaryImage)<img src="{{ img($item->product->primaryImage->path) }}" class="h-12 w-10 rounded-lg object-cover">@endif</td>
                    <td class="font-semibold">{{ $item->product?->name ?? '—' }}</td>
                    <td>{{ money($item->sale_price) }}</td>
                    <td>{{ $item->quantity_limit ?? '∞' }}</td>
                    <td>{{ $item->sold_count ?? 0 }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.flash-sales.products.destroy', [$flashSale, $item]) }}" onsubmit="return confirm('Remove?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-rose-600">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-ink-300">No products. <a href="{{ route('admin.flash-sales.edit', $flashSale) }}" class="text-plum-700 font-semibold">Add some →</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
