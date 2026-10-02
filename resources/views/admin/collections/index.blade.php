@extends('admin.layouts.app')
@section('title', 'Collections')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Collections</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $collections->total() }} collections</p>
    </div>
    <a href="{{ route('admin.collections.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add collection</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th></th><th>Name</th><th>Products</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($collections as $collection)
                <tr>
                    <td>@if($collection->image)<img src="{{ img($collection->image) }}" class="h-12 w-16 rounded-lg object-cover bg-paper-100">@endif</td>
                    <td>
                        <p class="font-semibold">{{ $collection->name }}</p>
                        @if($collection->subtitle)<p class="text-xs text-ink-300">{{ $collection->subtitle }}</p>@endif
                    </td>
                    <td>{{ $collection->products_count }}</td>
                    <td><span class="pill {{ status_badge($collection->is_active ? 'active' : 'inactive') }}">{{ $collection->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.collections.edit', $collection) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}" class="inline" onsubmit="return confirm('Delete this collection?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No collections.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $collections->links() }}</div>
@endsection
