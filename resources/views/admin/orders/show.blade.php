@extends('admin.layouts.app')
@section('title', 'Order '.$order->order_number)
@section('content')
@php $a = is_array($order->shipping_address) ? $order->shipping_address : []; @endphp
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">{{ $order->order_number }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $order->created_at?->format('d M Y H:i') }} · <span class="pill {{ status_badge($order->status) }}">{{ ucfirst($order->status) }}</span></p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-secondary" target="_blank">Invoice</a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost">Back</a>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3">
    <div class="xl:col-span-2 space-y-6">
        <div class="adm-card overflow-x-auto">
            <div class="adm-card-head"><h2 class="adm-card-title">Items</h2></div>
            <table class="adm-table">
                <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <p class="font-semibold">{{ $item->name }}</p>
                                @if($item->variant_label)<p class="text-xs text-ink-300">{{ $item->variant_label }}</p>@endif
                                @if($item->sku)<p class="text-xs text-ink-300">SKU: {{ $item->sku }}</p>@endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ money($item->price) }}</td>
                            <td class="font-semibold">{{ money($item->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="space-y-1 border-t border-ink/5 p-4 text-sm ml-auto max-w-xs">
                <div class="flex justify-between"><span class="text-ink-300">Subtotal</span><span>{{ money($order->subtotal) }}</span></div>
                @if($order->discount > 0)<div class="flex justify-between"><span class="text-ink-300">Discount</span><span>−{{ money($order->discount) }}</span></div>@endif
                <div class="flex justify-between"><span class="text-ink-300">Shipping</span><span>{{ $order->shipping_cost > 0 ? money($order->shipping_cost) : 'Free' }}</span></div>
                @if($order->tax > 0)<div class="flex justify-between"><span class="text-ink-300">Tax</span><span>{{ money($order->tax) }}</span></div>@endif
                <div class="flex justify-between font-bold text-base pt-2 border-t border-ink/10"><span>Total</span><span>{{ money($order->total) }}</span></div>
            </div>
        </div>

        <div class="adm-card p-6">
            <h2 class="adm-card-title mb-4">History</h2>
            <ol class="space-y-3">
                @forelse($order->histories as $h)
                    <li class="flex gap-3 text-sm">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-plum-600"></span>
                        <div>
                            <p><span class="font-semibold capitalize">{{ $h->status ?? $h->to_status ?? 'update' }}</span>
                                @if($h->comment ?? $h->note ?? null) — {{ $h->comment ?? $h->note }}@endif
                            </p>
                            <p class="text-xs text-ink-300">{{ $h->created_at?->format('d M Y H:i') }} · {{ $h->user?->name ?? 'System' }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-ink-300 text-sm">No history yet.</li>
                @endforelse
            </ol>
        </div>
    </div>

    <div class="space-y-6">
        <div class="adm-card p-6 text-sm space-y-2">
            <h2 class="adm-card-title mb-2">Customer</h2>
            <p class="font-semibold">{{ $order->customer_name }}</p>
            <p>{{ $order->customer_phone }}</p>
            <p class="text-ink-500">{{ $order->customer_email }}</p>
            @if($order->user)
                <a href="{{ route('admin.customers.show', $order->user) }}" class="text-plum-700 font-semibold hover:underline">View account →</a>
            @endif
            <hr class="border-ink/5 my-3">
            <h3 class="font-semibold">Shipping</h3>
            <p>{{ $a['name'] ?? $order->customer_name }}</p>
            <p class="text-ink-500">
                {{ $a['address_line'] ?? '' }}@if(!empty($a['area'])), {{ $a['area'] }}@endif<br>
                {{ $a['city'] ?? '' }}, {{ $a['district'] ?? '' }} {{ $a['postal_code'] ?? '' }}
            </p>
            @if($order->shippingMethod)<p class="mt-1">{{ $order->shippingMethod->name }}</p>@endif
            @if($order->coupon)<p class="mt-1">Coupon: <code>{{ $order->coupon->code }}</code></p>@endif
        </div>

        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="adm-card p-6 space-y-3">
            @csrf
            <h2 class="adm-card-title">Update status</h2>
            <select name="status" class="adm-select" required>
                @foreach($statuses as $s)
                    <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <textarea name="comment" rows="2" class="adm-input" placeholder="Comment (optional)"></textarea>
            <label class="adm-checkbox"><input type="checkbox" name="notify" value="1"> Notify customer</label>
            <button class="btn btn-primary w-full">Update status</button>
        </form>

        <form method="POST" action="{{ route('admin.orders.payment', $order) }}" class="adm-card p-6 space-y-3">
            @csrf
            <h2 class="adm-card-title">Payment</h2>
            <select name="payment_status" class="adm-select" required>
                @foreach(['unpaid','paid','failed','refunded','partially_refunded'] as $ps)
                    <option value="{{ $ps }}" @selected($order->payment_status === $ps)>{{ ucfirst(str_replace('_',' ',$ps)) }}</option>
                @endforeach
            </select>
            <select name="payment_method" class="adm-select">
                <option value="">Method…</option>
                @foreach(['cod','sslcommerz','bkash','nagad','card'] as $pm)
                    <option value="{{ $pm }}" @selected($order->payment_method === $pm)>{{ strtoupper($pm) }}</option>
                @endforeach
            </select>
            <input type="text" name="transaction_id" value="{{ $order->transaction_id }}" class="adm-input" placeholder="Transaction ID">
            <button class="btn btn-secondary w-full">Save payment</button>
        </form>

        <form method="POST" action="{{ route('admin.orders.tracking', $order) }}" class="adm-card p-6 space-y-3">
            @csrf
            <h2 class="adm-card-title">Tracking</h2>
            <input type="text" name="courier" value="{{ $order->courier }}" class="adm-input" placeholder="Courier">
            <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" class="adm-input" placeholder="Tracking number">
            <button class="btn btn-secondary w-full">Save tracking</button>
        </form>

        <form method="POST" action="{{ route('admin.orders.notes', $order) }}" class="adm-card p-6 space-y-3">
            @csrf
            <h2 class="adm-card-title">Notes</h2>
            <div>
                <label class="adm-label">Admin notes</label>
                <textarea name="admin_notes" rows="2" class="adm-input">{{ $order->admin_notes }}</textarea>
            </div>
            <div>
                <label class="adm-label">Customer notes</label>
                <textarea name="notes" rows="2" class="adm-input">{{ $order->notes }}</textarea>
            </div>
            <button class="btn btn-secondary w-full">Save notes</button>
        </form>
    </div>
</div>
@endsection
