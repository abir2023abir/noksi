@extends('admin.layouts.app')
@section('title', 'Edit attribute')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Edit attribute</h1><p class="mt-1 text-sm text-ink-500">{{ $attribute->name }}</p></div>
    <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <form method="POST" action="{{ route('admin.attributes.update', $attribute) }}" class="adm-card p-6 space-y-4">
        @csrf @method('PUT')
        <h2 class="adm-card-title">Details</h2>
        <div>
            <label class="adm-label">Name *</label>
            <input type="text" name="name" value="{{ old('name', $attribute->name) }}" class="adm-input" required>
            @error('name')<p class="adm-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="adm-label">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $attribute->slug) }}" class="adm-input">
        </div>
        <div>
            <label class="adm-label">Type *</label>
            <select name="type" class="adm-select" required>
                @foreach(['select','color','button'] as $t)
                    <option value="{{ $t }}" @selected(old('type', $attribute->type) === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="adm-label">Sort order</label>
            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $attribute->sort_order) }}" class="adm-input">
        </div>
        <label class="adm-checkbox"><input type="checkbox" name="is_filterable" value="1" @checked(old('is_filterable', $attribute->is_filterable))> Filterable</label>
        <div class="flex justify-end"><button class="btn btn-primary">Save</button></div>
    </form>

    <div class="adm-card p-6 space-y-4">
        <h2 class="adm-card-title">Values</h2>
        <ul class="divide-y divide-ink/5">
            @forelse($attribute->values as $value)
                <li class="flex items-center justify-between gap-2 py-2">
                    <span class="flex items-center gap-2 text-sm">
                        @if($value->color_code)<span class="h-3 w-3 rounded-full ring-1 ring-ink/10" style="background:{{ $value->color_code }}"></span>@endif
                        {{ $value->value }}
                    </span>
                    <form method="POST" action="{{ route('admin.attributes.values.destroy', [$attribute, $value]) }}" onsubmit="return confirm('Delete value?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-ghost btn-sm text-rose-600"><x-icon name="trash" class="h-4 w-4" /></button>
                    </form>
                </li>
            @empty
                <li class="py-4 text-sm text-ink-300">No values yet.</li>
            @endforelse
        </ul>
        <form method="POST" action="{{ route('admin.attributes.values.store', $attribute) }}" class="space-y-3 border-t border-ink/5 pt-4">
            @csrf
            <div>
                <label class="adm-label">Value *</label>
                <input type="text" name="value" class="adm-input" required>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="adm-label">Color code</label>
                    <input type="text" name="color_code" class="adm-input" placeholder="#hex">
                </div>
                <div>
                    <label class="adm-label">Sort</label>
                    <input type="number" min="0" name="sort_order" value="0" class="adm-input">
                </div>
            </div>
            <button class="btn btn-secondary btn-sm"><x-icon name="plus" class="h-4 w-4" /> Add value</button>
        </form>
    </div>
</div>
@endsection
