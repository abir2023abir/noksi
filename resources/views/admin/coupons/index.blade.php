@extends('admin.layouts.app')
@section('title', 'Coupons')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Coupons</h1><p class="mt-1 text-sm text-ink-500">{{ $coupons->total() }} coupons</p></div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add coupon</a>
</div>
<div class="adm-card overflow-x-auto">
    <table class="adm-table">
        <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Uses</th><th>Expires</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($coupons as $coupon)
                <tr>
                    <td class="font-mono font-semibold">{{ $coupon->code }}</td>
                    <td class="capitalize text-sm">{{ str_replace('_',' ',$coupon->type) }}</td>
                    <td>{{ $coupon->type === 'percent' ? $coupon->value.'%' : ($coupon->type === 'free_shipping' ? 'Free ship' : money($coupon->value)) }}</td>
                    <td>{{ $coupon->usages_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                    <td class="text-sm">{{ $coupon->expires_at?->format('d M Y') ?? '—' }}</td>
                    <td><span class="pill {{ status_badge($coupon->is_active ? 'active' : 'inactive') }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Delete coupon?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm text-rose-600">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-10 text-center text-ink-300">No coupons.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $coupons->links() }}</div>
@endsection
