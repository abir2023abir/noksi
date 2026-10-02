@extends('admin.layouts.app')
@section('title', 'Edit category')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Edit category</h1><p class="mt-1 text-sm text-ink-500">{{ $category->name }}</p></div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="adm-card max-w-2xl p-6 space-y-4">
    @csrf @method('PUT')
    @include('admin.categories._form', ['category' => $category])
    <div class="flex justify-end gap-2 pt-2">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary">Save</button>
    </div>
</form>
@endsection
