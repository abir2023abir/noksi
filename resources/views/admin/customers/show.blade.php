@extends('admin.layouts.app')
@section('title', $customer->name)
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">{{ $customer->name }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $customer->email }} · {{ $customer->phone }}</p>
    </div>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}">@csrf<button class="btn btn-secondary">{{ $customer->is_active ? 'Disable' : 'Enable' }}</button></form>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-ghost">Back</a>
    </div>
</div>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="adm-card p-6 space-y-2 text-sm lg:col-span-1">
        <h2 class="adm-card-title mb-2">Profile</h2>
        <p><span class="text-ink-300">Status:</span> <span class="pill {{ status_badge($customer->is_active ? 'active' : 'inactive') }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span></p>
        <p><span class="text-ink-300">Joined:</span> {{ $customer->created_at?->format('d M Y') }}</p>
        <h3 class="font-semibold pt-3">Addresses</h3>
        @forelse($customer->addresses as $addr)
            <div class="rounded-lg bg-paper-50 p-3 mt-2">
                <p class="font-medium">{{ $addr->label ?? 'Address' }} @if($addr->is_default)<span class="pill bg-emerald-100 text-emerald-800">Default</span>@endif</p>
                <p class="text-ink-500">{{ $addr->address_line }}{{ $addr->area ? ', '.$addr->area : '' }}<br>{{ $addr->city }}, {{ $addr->district }} {{ $addr->postal_code }}</p>
            </div>
        @empty
            <p class="text-ink-300">No addresses</p>
        @endforelse
        @if($customer->wishlists->isNotEmpty())
            <h3 class="font-semibold pt-3">Wishlist ({{ $customer->wishlists->count() }})</h3>
            <ul class="space-y-1">
                @foreach($customer->wishlists->take(10) as $w)
                    <li>{{ $w->product?->name ?? '—' }}</li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="adm-card overflow-x-auto lg:col-span-2">
        <div class="adm-card-head"><h2 class="adm-card-title">Recent orders</h2></div>
        <table class="adm-table">
            <thead><tr><th>Order</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($customer->orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold hover:text-plum-700">{{ $order->order_number }}</a></td>
                        <td>{{ money($order->total) }}</td>
                        <td><span class="pill {{ status_badge($order->status) }}">{{ ucfirst($order->status) }}</span></td>
                        <td class="text-sm">{{ $order->created_at?->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-10 text-center text-ink-300">No orders.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
