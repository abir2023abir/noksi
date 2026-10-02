@extends('admin.layouts.app')
@section('title', 'Banners')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Banners</h1><p class="mt-1 text-sm text-ink-500">{{ $banners->total() }} banners</p></div>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add banner</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th></th><th>Title</th><th>Position</th><th>Sort</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($banners as $banner)
                <tr>
                    <td><img src="{{ img($banner->image) }}" class="h-12 w-20 rounded-lg object-cover bg-paper-100" alt=""></td>
                    <td class="font-semibold">{{ $banner->title ?: '—' }}</td>
                    <td class="text-sm">{{ $banner->position ?: '—' }}</td>
                    <td>{{ $banner->sort_order }}</td>
                    <td><span class="pill {{ status_badge($banner->is_active ? 'active' : 'inactive') }}">{{ $banner->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="inline" onsubmit="return confirm('Delete banner?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm text-rose-600">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-ink-300">No banners.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $banners->links() }}</div>
@endsection
