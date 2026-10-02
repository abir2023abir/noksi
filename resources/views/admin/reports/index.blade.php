@extends('admin.layouts.app')
@section('title', 'Reports')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Reports</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.reports.sales', request()->query()) }}" class="btn btn-secondary">Sales</a>
        <a href="{{ route('admin.reports.products', request()->query()) }}" class="btn btn-secondary">Products</a>
        <a href="{{ route('admin.reports.customers', request()->query()) }}" class="btn btn-secondary">Customers</a>
    </div>
</div>
<form method="GET" class="mb-6 flex flex-wrap gap-2">
    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="adm-input max-w-[160px]">
    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="adm-input max-w-[160px]">
    <button class="btn btn-secondary">Apply</button>
</form>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="stat-card">
        <p class="text-xs font-bold uppercase tracking-wider text-ink-300">Revenue</p>
        <p class="mt-2 font-display text-3xl">{{ money($stats['revenue']) }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-bold uppercase tracking-wider text-ink-300">Orders</p>
        <p class="mt-2 font-display text-3xl">{{ $stats['orders'] }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-bold uppercase tracking-wider text-ink-300">Avg order</p>
        <p class="mt-2 font-display text-3xl">{{ money($stats['avg_order'] ?? 0) }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-bold uppercase tracking-wider text-ink-300">New customers</p>
        <p class="mt-2 font-display text-3xl">{{ $stats['customers'] }}</p>
    </div>
</div>
@endsection
