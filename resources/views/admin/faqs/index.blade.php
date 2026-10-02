@extends('admin.layouts.app')
@section('title', 'FAQs')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">FAQs</h1><p class="mt-1 text-sm text-ink-500">{{ $faqs->total() }} questions</p></div>
    <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add FAQ</a>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="category" value="{{ request('category') }}" placeholder="Category" class="adm-input max-w-xs">
    <button class="btn btn-secondary">Filter</button>
</form>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Question</th><th>Category</th><th>Sort</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($faqs as $faq)
                <tr>
                    <td class="font-semibold max-w-md">{{ $faq->question }}</td>
                    <td class="text-sm">{{ $faq->category ?: '—' }}</td>
                    <td>{{ $faq->sort_order }}</td>
                    <td><span class="pill {{ status_badge($faq->is_active ? 'active' : 'inactive') }}">{{ $faq->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="inline" onsubmit="return confirm('Delete FAQ?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm text-rose-600">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No FAQs.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $faqs->links() }}</div>
@endsection
