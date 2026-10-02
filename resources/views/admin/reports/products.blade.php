@extends('admin.layouts.app')
@section('title', 'Product report')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Product report</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</p>
    </div>
    <a href="{{ route('admin.reports.index', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}" class="btn btn-secondary">Overview</a>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="adm-input max-w-[160px]">
    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="adm-input max-w-[160px]">
    <button class="btn btn-secondary">Apply</button>
</form>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 adm-card overflow-x-auto">
        <div class="adm-card-head"><h2 class="adm-card-title">Top sellers</h2></div>
        <table class="adm-table">
            <thead><tr><th>Product</th><th>Qty sold</th><th>Revenue</th></tr></thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td class="font-semibold">{{ $row->name }}</td>
                        <td>{{ $row->qty_sold }}</td>
                        <td>{{ money($row->revenue) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-10 text-center text-ink-300">No product sales.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="adm-card overflow-x-auto">
        <div class="adm-card-head"><h2 class="adm-card-title">Low stock</h2></div>
        <table class="adm-table">
            <thead><tr><th>Product</th><th>Stock</th></tr></thead>
            <tbody>
                @forelse($lowStock as $p)
                    <tr>
                        <td><a href="{{ route('admin.products.show', $p) }}" class="font-semibold hover:text-plum-700">{{ $p->name }}</a></td>
                        <td class="text-amber-600 font-semibold">{{ $p->stock }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="py-8 text-center text-ink-300">All good</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
