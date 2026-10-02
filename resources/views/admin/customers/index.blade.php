@extends('admin.layouts.app')
@section('title', 'Customers')
@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl">Customers</h1>
    <p class="mt-1 text-sm text-ink-500">{{ $customers->total() }} customers</p>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search…" class="adm-input max-w-xs">
    <select name="status" class="adm-select max-w-[140px]">
        <option value="">Any status</option>
        <option value="active" @selected(request('status')==='active')>Active</option>
        <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
    </select>
    <button class="btn btn-secondary">Filter</button>
</form>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Name</th><th>Contact</th><th>Orders</th><th>Status</th><th>Joined</th><th></th></tr></thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td class="font-semibold">{{ $customer->name }}</td>
                    <td class="text-sm"><p>{{ $customer->email }}</p><p class="text-ink-300">{{ $customer->phone }}</p></td>
                    <td>{{ $customer->orders_count }}</td>
                    <td><span class="pill {{ status_badge($customer->is_active ? 'active' : 'inactive') }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-sm whitespace-nowrap">{{ $customer->created_at?->format('d M Y') }}</td>
                    <td class="text-right whitespace-nowrap">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-ghost btn-sm">View</a>
                        <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}" class="inline">@csrf<button class="btn btn-ghost btn-sm">{{ $customer->is_active ? 'Disable' : 'Enable' }}</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-ink-300">No customers.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $customers->links() }}</div>
@endsection
