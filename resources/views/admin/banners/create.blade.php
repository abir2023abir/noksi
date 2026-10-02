@extends('admin.layouts.app')
@section('title', 'Add banner')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Add banner</h1></div>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="adm-card max-w-2xl p-6 space-y-4">
    @csrf
    @include('admin.banners._form')
    <div class="flex justify-end gap-2"><a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">Create</button></div>
</form>
@endsection
