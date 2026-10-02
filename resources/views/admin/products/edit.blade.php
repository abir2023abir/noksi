@extends('admin.layouts.app')

@section('title', 'Edit product')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Edit product</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $product->name }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-secondary"><x-icon name="eye" class="h-4 w-4" /> View</a>
        <form method="POST" action="{{ route('admin.products.duplicate', $product) }}">@csrf<button class="btn btn-secondary">Duplicate</button></form>
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">Back</a>
    </div>
</div>

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.products._form', ['product' => $product])
    <div class="mt-6 flex justify-end gap-2">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary">Save changes</button>
    </div>
</form>
@endsection
