@extends('admin.layouts.app')
@section('title', 'Edit testimonial')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Edit testimonial</h1></div>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data" class="adm-card max-w-xl p-6 space-y-4">
    @csrf @method('PUT')
    @include('admin.testimonials._form', ['testimonial' => $testimonial])
    <div class="flex justify-end gap-2"><a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">Save</button></div>
</form>
@endsection
