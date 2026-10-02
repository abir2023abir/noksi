@extends('admin.layouts.app')
@section('title', 'Message')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">{{ $message->subject ?: 'Message' }}</h1>
        <p class="mt-1 text-sm text-ink-500">From {{ $message->name }} · {{ $message->email }} · {{ $message->created_at?->format('d M Y H:i') }}</p>
    </div>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Delete message?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger-soft">Delete</button>
        </form>
        <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
<div class="grid gap-6 lg:grid-cols-2">
    <div class="adm-card p-6 space-y-3">
        <h2 class="adm-card-title">Message</h2>
        @if($message->phone)<p class="text-sm text-ink-500">Phone: {{ $message->phone }}</p>@endif
        <div class="prose prose-sm max-w-none whitespace-pre-wrap">{{ $message->message ?? $message->body }}</div>
        @if($message->reply ?? $message->admin_reply ?? null)
            <div class="mt-4 rounded-xl bg-paper-50 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-ink-300 mb-1">Your reply</p>
                <p class="whitespace-pre-wrap text-sm">{{ $message->reply ?? $message->admin_reply }}</p>
            </div>
        @endif
    </div>
    <form method="POST" action="{{ route('admin.messages.reply', $message) }}" class="adm-card p-6 space-y-4 h-fit">
        @csrf
        <h2 class="adm-card-title">Reply</h2>
        <textarea name="reply" rows="8" class="adm-input" required placeholder="Write a reply…">{{ old('reply', $message->reply ?? $message->admin_reply) }}</textarea>
        @error('reply')<p class="adm-error">{{ $errors->first('reply') }}</p>@enderror
        <button class="btn btn-primary">Send reply</button>
    </form>
</div>
@endsection
