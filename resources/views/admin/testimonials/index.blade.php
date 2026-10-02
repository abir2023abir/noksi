@extends('admin.layouts.app')
@section('title', 'Testimonials')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Testimonials</h1><p class="mt-1 text-sm text-ink-500">{{ $testimonials->total() }}</p></div>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Name</th><th>Rating</th><th>Body</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($testimonials as $t)
                <tr>
                    <td class="font-semibold">{{ $t->name }}@if($t->role)<span class="block text-xs text-ink-300">{{ $t->role }}</span>@endif</td>
                    <td>{{ $t->rating ?? '—' }}</td>
                    <td class="max-w-sm truncate text-sm">{{ $t->body }}</td>
                    <td><span class="pill {{ status_badge($t->is_active ? 'active' : 'inactive') }}">{{ $t->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm text-rose-600">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No testimonials.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $testimonials->links() }}</div>
@endsection
