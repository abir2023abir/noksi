@extends('admin.layouts.app')
@section('title', 'Add collection')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Add collection</h1></div>
    <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.collections.store') }}" enctype="multipart/form-data" class="adm-card max-w-2xl p-6 space-y-4">
    @csrf
    @include('admin.collections._form')
    <div class="flex justify-end gap-2"><a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">Create</button></div>
</form>
@endsection
