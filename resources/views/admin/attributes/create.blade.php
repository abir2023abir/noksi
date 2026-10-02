@extends('admin.layouts.app')
@section('title', 'Add attribute')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="font-display text-3xl">Add attribute</h1></div>
    <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Back</a>
</div>
<form method="POST" action="{{ route('admin.attributes.store') }}" class="adm-card max-w-xl p-6 space-y-4">
    @csrf
    <div>
        <label class="adm-label">Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" class="adm-input" required>
        @error('name')<p class="adm-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="adm-label">Slug</label>
        <input type="text" name="slug" value="{{ old('slug') }}" class="adm-input" placeholder="auto">
    </div>
    <div>
        <label class="adm-label">Type *</label>
        <select name="type" class="adm-select" required>
            @foreach(['select','color','button'] as $t)
                <option value="{{ $t }}" @selected(old('type') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="adm-label">Sort order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}" class="adm-input">
    </div>
    <label class="adm-checkbox"><input type="checkbox" name="is_filterable" value="1" @checked(old('is_filterable', true))> Filterable</label>
    <div class="flex justify-end gap-2"><a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">Create</button></div>
</form>
@endsection
