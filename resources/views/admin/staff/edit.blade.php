@extends('admin.layouts.app')
@section('title', 'Edit staff')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Edit staff</h1><p class="mt-1 text-sm text-ink-500">{{ $staff->name }}</p></div>
    <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.staff.update', $staff) }}" enctype="multipart/form-data" class="adm-card max-w-xl p-6 space-y-4">
    @csrf @method('PUT')
    @include('admin.staff._form', ['staff' => $staff])
    <div class="flex justify-end gap-2"><a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">Save</button></div>
</form>
@endsection
