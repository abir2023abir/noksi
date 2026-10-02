@extends('admin.layouts.app')
@section('title', 'Edit brand')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Edit brand</h1><p class="mt-1 text-sm text-ink-500">{{ $brand->name }}</p></div>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data" class="adm-card max-w-xl p-6 space-y-4">
    @csrf @method('PUT')
    @include('admin.brands._form', ['brand' => $brand])
    <div class="flex justify-end gap-2"><a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">Save</button></div>
</form>
@endsection
