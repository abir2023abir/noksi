@php
    $collection = $collection ?? null;
    $selected = old('product_ids', $collection?->products?->pluck('id')->all() ?? []);
@endphp
<div>
    <label class="adm-label">Name *</label>
    <input type="text" name="name" value="{{ old('name', $collection?->name) }}" class="adm-input" required>
    @error('name')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $collection?->slug) }}" class="adm-input" placeholder="auto">
    </div>
    <div>
        <label class="adm-label">Subtitle</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $collection?->subtitle) }}" class="adm-input">
    </div>
</div>
<div>
    <label class="adm-label">Description</label>
    <textarea name="description" rows="3" class="adm-input">{{ old('description', $collection?->description) }}</textarea>
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Color</label>
        <input type="text" name="color" value="{{ old('color', $collection?->color) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Sort order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $collection?->sort_order ?? 0) }}" class="adm-input">
    </div>
</div>
<div>
    <label class="adm-label">Image</label>
    @if($collection?->image)<img src="{{ img($collection->image) }}" class="mb-2 h-20 w-32 rounded-lg object-cover">@endif
    <input type="file" name="image" accept="image/*" class="adm-input">
</div>
<label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $collection?->is_active ?? true))> Active</label>
<div>
    <label class="adm-label">Products</label>
    <select name="product_ids[]" class="adm-select h-48" multiple>
        @foreach($products as $p)
            <option value="{{ $p->id }}" @selected(in_array($p->id, (array) $selected))>{{ $p->name }}</option>
        @endforeach
    </select>
    <p class="adm-hint">Hold Ctrl/Cmd to select multiple</p>
</div>
