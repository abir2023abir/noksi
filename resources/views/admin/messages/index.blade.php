@extends('admin.layouts.app')
@section('title', 'Messages')
@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl">Messages</h1>
    <p class="mt-1 text-sm text-ink-500">{{ $messages->total() }} messages</p>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="adm-input max-w-xs">
    <select name="status" class="adm-select max-w-[140px]">
        <option value="">All</option>
        <option value="unread" @selected(request('status')==='unread')>Unread</option>
        <option value="read" @selected(request('status')==='read')>Read</option>
        <option value="replied" @selected(request('status')==='replied')>Replied</option>
    </select>
    <button class="btn btn-secondary">Filter</button>
</form>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>From</th><th>Subject</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
            @forelse($messages as $message)
                <tr class="{{ ! $message->is_read ? 'bg-plum-50/40' : '' }}">
                    <td>
                        <p class="font-semibold">{{ $message->name }}</p>
                        <p class="text-xs text-ink-300">{{ $message->email }}</p>
                    </td>
                    <td class="max-w-xs truncate">{{ $message->subject ?: Str::limit($message->message ?? $message->body ?? '', 60) }}</td>
                    <td>
                        @if($message->is_replied)<span class="pill bg-emerald-100 text-emerald-800">Replied</span>
                        @elseif($message->is_read)<span class="pill bg-slate-100 text-slate-700">Read</span>
                        @else<span class="pill bg-amber-100 text-amber-800">Unread</span>@endif
                    </td>
                    <td class="text-sm whitespace-nowrap">{{ $message->created_at?->format('d M Y') }}</td>
                    <td class="text-right"><a href="{{ route('admin.messages.show', $message) }}" class="btn btn-ghost btn-sm">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-300">No messages.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $messages->links() }}</div>
@endsection
