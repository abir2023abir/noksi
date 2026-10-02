@extends('admin.layouts.app')
@section('title', 'Customer report')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Customer report</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</p>
    </div>
    <a href="{{ route('admin.reports.index', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}" class="btn btn-secondary">Overview</a>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="adm-input max-w-[160px]">
    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="adm-input max-w-[160px]">
    <button class="btn btn-secondary">Apply</button>
</form>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Customer</th><th>Orders</th><th>Revenue</th><th></th></tr></thead>
        <tbody>
            @forelse($rows as $customer)
                <tr>
                    <td>
                        <p class="font-semibold">{{ $customer->name }}</p>
                        <p class="text-xs text-ink-300">{{ $customer->email }}</p>
                    </td>
                    <td>{{ $customer->orders_count }}</td>
                    <td class="font-semibold">{{ money($customer->revenue ?? 0) }}</td>
                    <td class="text-right"><a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-10 text-center text-ink-300">No customer data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
