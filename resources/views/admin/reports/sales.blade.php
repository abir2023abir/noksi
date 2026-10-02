@extends('admin.layouts.app')
@section('title', 'Sales report')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Sales report</h1>
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
        <thead><tr><th>Day</th><th>Orders</th><th>Revenue</th><th>Discounts</th></tr></thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td class="font-semibold whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($row->day)->format('d M Y') }}</td>
                    <td>{{ $row->orders }}</td>
                    <td>{{ money($row->revenue) }}</td>
                    <td>{{ money($row->discounts) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-10 text-center text-ink-300">No sales in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
