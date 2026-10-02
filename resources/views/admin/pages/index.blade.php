@extends('admin.layouts.app')
@section('title', 'Pages')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Pages</h1><p class="mt-1 text-sm text-ink-500">{{ $pages->total() }} pages</p></div>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add page</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Title</th><th>Slug</th><th>Footer</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($pages as $page)
                <tr>
                    <td class="font-semibold">{{ $page->title }}</td>
                    <td class="text-sm font-mono text-ink-300">{{ $page->slug }}</td>
                    <td>{{ $page->show_in_footer ? 'Yes' : 'No' }}</td>
                    <td><span class="pill {{ status_badge($page->is_active ? 'active' : 'inactive') }}">{{ $page->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="inline" onsubmit="return confirm('Delete page?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm text-rose-600">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No pages.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $pages->links() }}</div>
@endsection
