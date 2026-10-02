@extends('admin.layouts.app')
@section('title', 'New post')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">New post</h1></div>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data" class="adm-card max-w-3xl p-6 space-y-4">
    @csrf
    @include('admin.posts._form')
    <div class="flex justify-end gap-2"><a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">Create</button></div>
</form>
@endsection
