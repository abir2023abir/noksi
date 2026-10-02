@extends('admin.layouts.app')
@section('title', 'Shipping')
@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl">Shipping</h1>
    <p class="mt-1 text-sm text-ink-500">Zones and delivery methods</p>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div class="space-y-4">
        <div class="adm-card overflow-x-auto">
            <div class="adm-card-head"><h2 class="adm-card-title">Zones</h2></div>
            <table class="adm-table">
                <thead><tr><th>Name</th><th>Districts</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($zones as $zone)
                        <tr>
                            <td class="font-semibold align-top">{{ $zone->name }}</td>
                            <td class="text-xs text-ink-500 max-w-xs">{{ is_array($zone->districts) ? implode(', ', $zone->districts) : ($zone->districts ?: '—') }}</td>
                            <td><span class="pill {{ status_badge($zone->is_active ? 'active' : 'inactive') }}">{{ $zone->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="align-top">
                                <details class="text-right">
                                    <summary class="btn btn-ghost btn-sm cursor-pointer list-none">Edit</summary>
                                    <form method="POST" action="{{ route('admin.shipping.zones.update', $zone) }}" class="mt-2 space-y-2 text-left min-w-[220px]">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $zone->name }}" class="adm-input" required>
                                        <textarea name="districts" rows="2" class="adm-input" placeholder="Comma or line separated">{{ is_array($zone->districts) ? implode(', ', $zone->districts) : $zone->districts }}</textarea>
                                        <label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked($zone->is_active)> Active</label>
                                        <button class="btn btn-secondary btn-sm w-full">Save</button>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('admin.shipping.zones.destroy', $zone) }}" class="mt-1" onsubmit="return confirm('Delete zone?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-ink-300">No zones.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <form method="POST" action="{{ route('admin.shipping.zones.store') }}" class="adm-card p-6 space-y-3">
            @csrf
            <h2 class="adm-card-title">Add zone</h2>
            <input type="text" name="name" class="adm-input" placeholder="Name *" required>
            <textarea name="districts" rows="2" class="adm-input" placeholder="Districts (comma or newline)"></textarea>
            <label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            <button class="btn btn-primary btn-sm">Create zone</button>
        </form>
    </div>

    <div class="space-y-4">
        <div class="adm-card overflow-x-auto">
            <div class="adm-card-head"><h2 class="adm-card-title">Methods</h2></div>
            <table class="adm-table">
                <thead><tr><th>Name</th><th>Zone</th><th>Cost</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($methods as $method)
                        <tr>
                            <td>
                                <p class="font-semibold">{{ $method->name }}</p>
                                @if($method->estimated_days)<p class="text-xs text-ink-300">{{ $method->estimated_days }}</p>@endif
                            </td>
                            <td class="text-sm">{{ $method->zone?->name ?? 'All' }}</td>
                            <td>{{ money($method->cost) }}@if($method->free_above)<span class="block text-xs text-ink-300">Free ≥ {{ money($method->free_above) }}</span>@endif</td>
                            <td><span class="pill {{ status_badge($method->is_active ? 'active' : 'inactive') }}">{{ $method->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="align-top">
                                <details>
                                    <summary class="btn btn-ghost btn-sm cursor-pointer list-none">Edit</summary>
                                    <form method="POST" action="{{ route('admin.shipping.methods.update', $method) }}" class="mt-2 space-y-2 min-w-[240px]">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $method->name }}" class="adm-input" required>
                                        <select name="shipping_zone_id" class="adm-select">
                                            <option value="">All zones</option>
                                            @foreach($zones as $z)
                                                <option value="{{ $z->id }}" @selected($method->shipping_zone_id == $z->id)>{{ $z->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="description" value="{{ $method->description }}" class="adm-input" placeholder="Description">
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="number" step="0.01" min="0" name="cost" value="{{ $method->cost }}" class="adm-input" required>
                                            <input type="number" step="0.01" min="0" name="free_above" value="{{ $method->free_above }}" class="adm-input" placeholder="Free above">
                                        </div>
                                        <input type="text" name="estimated_days" value="{{ $method->estimated_days }}" class="adm-input" placeholder="Est. days">
                                        <input type="number" min="0" name="sort_order" value="{{ $method->sort_order }}" class="adm-input">
                                        <label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked($method->is_active)> Active</label>
                                        <button class="btn btn-secondary btn-sm w-full">Save</button>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('admin.shipping.methods.destroy', $method) }}" onsubmit="return confirm('Delete method?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-ink-300">No methods.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <form method="POST" action="{{ route('admin.shipping.methods.store') }}" class="adm-card p-6 space-y-3">
            @csrf
            <h2 class="adm-card-title">Add method</h2>
            <input type="text" name="name" class="adm-input" placeholder="Name *" required>
            <select name="shipping_zone_id" class="adm-select">
                <option value="">All zones</option>
                @foreach($zones as $z)
                    <option value="{{ $z->id }}">{{ $z->name }}</option>
                @endforeach
            </select>
            <input type="text" name="description" class="adm-input" placeholder="Description">
            <div class="grid grid-cols-2 gap-2">
                <input type="number" step="0.01" min="0" name="cost" class="adm-input" placeholder="Cost *" required>
                <input type="number" step="0.01" min="0" name="free_above" class="adm-input" placeholder="Free above">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input type="text" name="estimated_days" class="adm-input" placeholder="Est. days">
                <input type="number" min="0" name="sort_order" value="0" class="adm-input">
            </div>
            <label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            <button class="btn btn-primary btn-sm">Create method</button>
        </form>
    </div>
</div>
@endsection
