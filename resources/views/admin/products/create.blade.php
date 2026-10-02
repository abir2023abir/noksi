@extends('admin.layouts.app')

@section('title', 'Add product')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Add product</h1>
        <p class="mt-1 text-sm text-ink-500">Create a new catalog item</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back</a>
</div>

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.products._form')
    <div class="mt-6 flex justify-end gap-2">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary">Create product</button>
    </div>
</form>
@endsection
