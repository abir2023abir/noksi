@extends('admin.layouts.app')
@section('title', 'Reviews')
@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl">Reviews</h1>
    <p class="mt-1 text-sm text-ink-500">{{ $reviews->total() }} reviews</p>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="adm-input max-w-xs">
    <select name="status" class="adm-select max-w-[140px]">
        <option value="">Any status</option>
        @foreach(['pending','approved','rejected'] as $s)
            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button class="btn btn-secondary">Filter</button>
</form>
<div class="space-y-4">
    @forelse($reviews as $review)
        <div class="adm-card p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="font-semibold">{{ $review->title ?: 'Review' }} · {{ $review->rating }}/5</p>
                    <p class="text-sm text-ink-500">{{ $review->product?->name ?? '—' }} · {{ $review->user?->name ?? $review->name ?? 'Guest' }} · {{ $review->created_at?->format('d M Y') }}</p>
                    <p class="mt-2 text-sm">{{ $review->body }}</p>
                    @if($review->admin_reply)<p class="mt-2 rounded-lg bg-paper-50 p-3 text-sm"><span class="font-semibold">Reply:</span> {{ $review->admin_reply }}</p>@endif
                </div>
                <span class="pill {{ status_badge($review->status) }}">{{ ucfirst($review->status) }}</span>
            </div>
            <div class="mt-4 flex flex-wrap items-end gap-2">
                <form method="POST" action="{{ route('admin.reviews.status', $review) }}" class="flex gap-1">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button class="btn btn-secondary btn-sm">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.status', $review) }}" class="flex gap-1">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button class="btn btn-ghost btn-sm">Reject</button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.reply', $review) }}" class="flex flex-1 flex-wrap gap-2 min-w-[220px]">
                    @csrf
                    <input type="text" name="admin_reply" value="{{ $review->admin_reply }}" class="adm-input flex-1" placeholder="Reply…" required>
                    <button class="btn btn-secondary btn-sm">Reply</button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete review?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="adm-card py-12 text-center text-ink-300">No reviews.</div>
    @endforelse
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
@endsection
