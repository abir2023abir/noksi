@extends('admin.layouts.app')
@section('title', 'Edit coupon')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Edit coupon</h1><p class="mt-1 text-sm text-ink-500">{{ $coupon->code }}</p></div>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="adm-card max-w-2xl p-6 space-y-4">
    @csrf @method('PUT')
    @include('admin.coupons._form', ['coupon' => $coupon])
    <div class="flex justify-end gap-2"><a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">Save</button></div>
</form>
@endsection
