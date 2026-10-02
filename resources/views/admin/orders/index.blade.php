@extends('admin.layouts.app')
@section('title', 'Orders')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Orders</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $orders->total() }} orders</p>
    </div>
    <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn btn-secondary">Export CSV</a>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search order / customer…" class="adm-input max-w-xs">
    <select name="status" class="adm-select max-w-[150px]">
        <option value="">Any status</option>
        @foreach($statuses as $s)
            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <select name="payment_status" class="adm-select max-w-[150px]">
        <option value="">Any payment</option>
        @foreach(['unpaid','paid','failed','refunded','partially_refunded'] as $ps)
            <option value="{{ $ps }}" @selected(request('payment_status')===$ps)>{{ ucfirst(str_replace('_',' ',$ps)) }}</option>
        @endforeach
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="adm-input max-w-[150px]">
    <input type="date" name="to" value="{{ request('to') }}" class="adm-input max-w-[150px]">
    <button class="btn btn-secondary">Filter</button>
</form>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th><th></th></tr></thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold hover:text-plum-700">{{ $order->order_number }}</a></td>
                    <td>
                        <p class="text-sm font-medium">{{ $order->customer_name }}</p>
                        <p class="text-xs text-ink-300">{{ $order->customer_phone }}</p>
                    </td>
                    <td>{{ $order->items_count }}</td>
                    <td class="font-semibold">{{ money($order->total) }}</td>
                    <td><span class="pill {{ status_badge($order->status) }}">{{ ucfirst($order->status) }}</span></td>
                    <td><span class="pill {{ status_badge($order->payment_status) }}">{{ ucfirst($order->payment_status) }}</span></td>
                    <td class="text-sm whitespace-nowrap">{{ $order->created_at?->format('d M Y') }}</td>
                    <td class="text-right"><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="py-10 text-center text-ink-300">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
