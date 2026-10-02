@extends('admin.layouts.app')
@section('title', 'Blog posts')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Blog</h1><p class="mt-1 text-sm text-ink-500">{{ $posts->total() }} posts</p></div>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> New post</a>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="adm-input max-w-xs">
    <select name="status" class="adm-select max-w-[140px]">
        <option value="">Any status</option>
        <option value="draft" @selected(request('status')==='draft')>Draft</option>
        <option value="published" @selected(request('status')==='published')>Published</option>
    </select>
    <button class="btn btn-secondary">Filter</button>
</form>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 adm-card overflow-x-auto">
        <table class="adm-table">
            <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td class="font-semibold">{{ $post->title }}</td>
                        <td class="text-sm">{{ $post->category?->name ?? '—' }}</td>
                        <td><span class="pill {{ status_badge($post->status) }}">{{ ucfirst($post->status) }}</span></td>
                        <td class="text-sm">{{ ($post->published_at ?? $post->created_at)?->format('d M Y') }}</td>
                        <td class="whitespace-nowrap text-right">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete post?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm text-rose-600">Delete</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-ink-300">No posts.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="adm-card p-6 space-y-4 h-fit">
        <h2 class="adm-card-title">Categories</h2>
        <ul class="divide-y divide-ink/5 text-sm">
            @foreach($categories as $cat)
                <li class="flex items-center justify-between gap-2 py-2">
                    <span>{{ $cat->name }}</span>
                    <form method="POST" action="{{ route('admin.blog-categories.destroy', $cat) }}" onsubmit="return confirm('Delete category?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-ghost btn-sm text-rose-600"><x-icon name="trash" class="h-3.5 w-3.5" /></button>
                    </form>
                </li>
            @endforeach
        </ul>
        <form method="POST" action="{{ route('admin.blog-categories.store') }}" class="space-y-2 border-t border-ink/5 pt-4">
            @csrf
            <input type="text" name="name" class="adm-input" placeholder="New category" required>
            <button class="btn btn-secondary btn-sm w-full">Add category</button>
        </form>
    </div>
</div>
<div class="mt-4">{{ $posts->links() }}</div>
@endsection
