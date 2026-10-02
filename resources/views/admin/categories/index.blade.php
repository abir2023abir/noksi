@extends('admin.layouts.app')
@section('title', 'Categories')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Categories</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $categories->total() }} categories</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add category</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Name</th><th>Parent</th><th>Products</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td class="font-semibold">{{ $category->name }}@if($category->name_bn)<span class="ml-1 text-xs text-ink-300">{{ $category->name_bn }}</span>@endif</td>
                    <td class="text-sm">{{ $category->parent?->name ?? '—' }}</td>
                    <td>{{ $category->products_count }}</td>
                    <td><span class="pill {{ status_badge($category->is_active ? 'active' : 'inactive') }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No categories.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
