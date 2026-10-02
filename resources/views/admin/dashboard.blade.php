@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="font-display text-3xl">Dashboard</h1>
    <p class="mt-1 text-ink-500">{{ now()->format('l, d M Y') }} · Welcome back, {{ auth()->user()->name }}</p>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="stat-card">
        <p class="text-xs font-bold uppercase tracking-wider text-ink-300">Revenue today</p>
        <p class="mt-2 font-display text-3xl">{{ money($stats['revenue_today']) }}</p>
        <p class="mt-1 text-sm text-ink-500">{{ $stats['orders_today'] }} orders</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-bold uppercase tracking-wider text-ink-300">Revenue this month</p>
        <p class="mt-2 font-display text-3xl">{{ money($stats['revenue_month']) }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-bold uppercase tracking-wider text-ink-300">Pending orders</p>
        <p class="mt-2 font-display text-3xl text-amber-600">{{ $stats['orders_pending'] }}</p>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="mt-1 inline-block text-sm font-semibold text-plum-700 hover:underline">Review →</a>
    </div>
    <div class="stat-card">
        <p class="text-xs font-bold uppercase tracking-wider text-ink-300">Attention</p>
        <p class="mt-2 text-sm space-y-1">
            <a href="{{ route('admin.inventory.index') }}" class="block hover:text-plum-700">{{ $stats['low_stock'] }} low stock</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="block hover:text-plum-700">{{ $stats['pending_reviews'] }} reviews</a>
            <a href="{{ route('admin.messages.index') }}" class="block hover:text-plum-700">{{ $stats['unread_messages'] }} messages</a>
        </p>
    </div>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    <div class="adm-card xl:col-span-2">
        <div class="adm-card-head">
            <h2 class="adm-card-title">Sales (30 days)</h2>
            <div class="flex gap-2" x-data>
                <button type="button" class="btn btn-secondary btn-xs" @click="$dispatch('chart-days', 7)">7d</button>
                <button type="button" class="btn btn-secondary btn-xs" @click="$dispatch('chart-days', 30)">30d</button>
                <button type="button" class="btn btn-secondary btn-xs" @click="$dispatch('chart-days', 90)">90d</button>
            </div>
        </div>
        <div class="p-6" x-data="salesChart()" x-init="load(30)" @chart-days.window="load($event.detail)">
            <canvas id="salesChart" height="120"></canvas>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-head"><h2 class="adm-card-title">Top products</h2></div>
        <ul class="divide-y divide-ink/5 px-2">
            @foreach($topProducts as $p)
                <li class="flex items-center gap-3 px-4 py-3">
                    <img src="{{ $p->image_url }}" class="h-10 w-8 rounded-lg object-cover bg-paper-100" alt="">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('admin.products.show', $p) }}" class="block truncate text-sm font-semibold hover:text-plum-700">{{ $p->name }}</a>
                        <p class="text-xs text-ink-300">{{ $p->sold_count }} sold</p>
                    </div>
                    <span class="text-sm font-semibold">{{ money($p->price) }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-5">
    <div class="adm-card xl:col-span-3">
        <div class="adm-card-head">
            <h2 class="adm-card-title">Recent orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-plum-700 hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="adm-table">
                <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach($recentOrders as $o)
                        <tr>
                            <td class="font-mono text-xs font-semibold">{{ $o->order_number }}</td>
                            <td>{{ $o->customer_name }}</td>
                            <td class="font-semibold">{{ money($o->total) }}</td>
                            <td><span class="pill bg-{{ $o->status_color }}-50 text-{{ $o->status_color }}-700 ring-{{ $o->status_color }}-200">{{ $o->status }}</span></td>
                            <td><a href="{{ route('admin.orders.show', $o) }}" class="text-sm font-semibold text-plum-700">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="adm-card xl:col-span-2">
        <div class="adm-card-head">
            <h2 class="adm-card-title">Low stock</h2>
            <a href="{{ route('admin.inventory.index') }}" class="text-sm font-semibold text-plum-700 hover:underline">Inventory</a>
        </div>
        <ul class="divide-y divide-ink/5">
            @forelse($lowStock as $p)
                <li class="flex items-center gap-3 px-6 py-3">
                    <img src="{{ $p->image_url }}" class="h-10 w-8 rounded-lg object-cover" alt="">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ $p->name }}</p>
                        <p class="text-xs {{ $p->stock === 0 ? 'text-rose-600' : 'text-amber-600' }}">{{ $p->stock }} left</p>
                    </div>
                </li>
            @empty
                <li class="px-6 py-8 text-center text-sm text-ink-300">All good — no low stock.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
function salesChart() {
    return {
        chart: null,
        async load(days) {
            const res = await fetch(`{{ route('admin.dashboard.chart') }}?days=${days}`);
            const data = await res.json();
            const ctx = document.getElementById('salesChart');
            if (this.chart) this.chart.destroy();
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        { label: 'Revenue', data: data.revenue, borderColor: '#5B1B4F', backgroundColor: 'rgba(91,27,79,0.08)', fill: true, tension: 0.35, yAxisID: 'y' },
                        { label: 'Orders', data: data.orders, borderColor: '#0F766E', backgroundColor: 'transparent', tension: 0.35, yAxisID: 'y1' },
                    ],
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: { position: 'left', grid: { color: 'rgba(20,17,26,0.04)' } },
                        y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } },
                    },
                    plugins: { legend: { position: 'bottom' } },
                },
            });
        },
    };
}
</script>
@endpush
