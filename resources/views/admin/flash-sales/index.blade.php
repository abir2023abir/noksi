@extends('admin.layouts.app')
@section('title', 'Flash sales')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Flash sales</h1><p class="mt-1 text-sm text-ink-500">{{ $flashSales->total() }} sales</p></div>
    <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add flash sale</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Title</th><th>Period</th><th>Products</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($flashSales as $sale)
                <tr>
                    <td class="font-semibold">{{ $sale->title }}</td>
                    <td class="text-sm whitespace-nowrap">{{ $sale->starts_at?->format('d M') }} – {{ $sale->ends_at?->format('d M Y') }}</td>
                    <td>{{ $sale->items_count }}</td>
                    <td><span class="pill {{ status_badge($sale->is_active ? 'active' : 'inactive') }}">{{ $sale->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.flash-sales.show', $sale) }}" class="btn btn-ghost btn-sm">View</a>
                        <a href="{{ route('admin.flash-sales.edit', $sale) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.flash-sales.destroy', $sale) }}" class="inline" onsubmit="return confirm('Delete flash sale?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm text-rose-600">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No flash sales.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $flashSales->links() }}</div>
@endsection
