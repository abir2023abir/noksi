@extends('admin.layouts.app')
@section('title', 'Newsletter')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Newsletter</h1><p class="mt-1 text-sm text-ink-500">{{ $subscribers->total() }} subscribers</p></div>
    <a href="{{ route('admin.newsletter.export') }}" class="btn btn-secondary">Export CSV</a>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="adm-input max-w-xs">
    <select name="status" class="adm-select max-w-[140px]">
        <option value="">Any status</option>
        <option value="active" @selected(request('status')==='active')>Active</option>
        <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
    </select>
    <button class="btn btn-secondary">Filter</button>
</form>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Email</th><th>Name</th><th>Source</th><th>Status</th><th>Joined</th><th></th></tr></thead>
        <tbody>
            @forelse($subscribers as $s)
                <tr>
                    <td class="font-semibold">{{ $s->email }}</td>
                    <td class="text-sm">{{ $s->name ?: '—' }}</td>
                    <td class="text-sm">{{ $s->source ?: '—' }}</td>
                    <td><span class="pill {{ status_badge($s->is_active ? 'active' : 'inactive') }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-sm whitespace-nowrap">{{ $s->created_at?->format('d M Y') }}</td>
                    <td class="text-right">
                        <form method="POST" action="{{ route('admin.newsletter.destroy', $s) }}" onsubmit="return confirm('Remove subscriber?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-rose-600">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-ink-300">No subscribers.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $subscribers->links() }}</div>
@endsection
