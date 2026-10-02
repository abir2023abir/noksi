@extends('admin.layouts.app')
@section('title', 'Brands')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Brands</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $brands->total() }} brands</p>
    </div>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add brand</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th></th><th>Name</th><th>Products</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($brands as $brand)
                <tr>
                    <td>@if($brand->logo)<img src="{{ img($brand->logo) }}" class="h-10 w-10 rounded-lg object-contain bg-paper-100">@endif</td>
                    <td class="font-semibold">{{ $brand->name }}</td>
                    <td>{{ $brand->products_count }}</td>
                    <td><span class="pill {{ status_badge($brand->is_active ? 'active' : 'inactive') }}">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="inline" onsubmit="return confirm('Delete this brand?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No brands.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $brands->links() }}</div>
@endsection
