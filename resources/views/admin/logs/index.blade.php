@extends('admin.layouts.app')
@section('title', 'Activity log')
@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl">Activity</h1>
    <p class="mt-1 text-sm text-ink-500">{{ $logs->total() }} entries</p>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="adm-input max-w-xs">
    <input type="number" name="user_id" value="{{ request('user_id') }}" placeholder="User ID" class="adm-input max-w-[120px]">
    <button class="btn btn-secondary">Filter</button>
</form>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>When</th><th>User</th><th>Action</th><th>Description</th></tr></thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="text-sm whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i') }}</td>
                    <td class="text-sm">{{ $log->user?->name ?? '—' }}</td>
                    <td class="font-mono text-xs">{{ $log->action }}</td>
                    <td class="text-sm max-w-lg">{{ $log->description }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-10 text-center text-ink-300">No activity logged.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
