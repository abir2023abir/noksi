@extends('admin.layouts.app')
@section('title', 'Add coupon')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Add coupon</h1></div>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.coupons.store') }}" class="adm-card max-w-2xl p-6 space-y-4">
    @csrf
    @include('admin.coupons._form')
    <div class="flex justify-end gap-2"><a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">Create</button></div>
</form>
@endsection
