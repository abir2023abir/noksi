@php $category = $category ?? null; @endphp
<div>
    <label class="adm-label">Parent</label>
    <select name="parent_id" class="adm-select">
        <option value="">None (root)</option>
        @foreach($parents as $p)
            <option value="{{ $p->id }}" @selected(old('parent_id', $category?->parent_id) == $p->id)>{{ $p->name }}</option>
        @endforeach
    </select>
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Name *</label>
        <input type="text" name="name" value="{{ old('name', $category?->name) }}" class="adm-input" required>
        @error('name')<p class="adm-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="adm-label">Name (Bangla)</label>
        <input type="text" name="name_bn" value="{{ old('name_bn', $category?->name_bn) }}" class="adm-input">
    </div>
</div>
<div>
    <label class="adm-label">Slug</label>
    <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}" class="adm-input" placeholder="auto">
</div>
<div>
    <label class="adm-label">Description</label>
    <textarea name="description" rows="3" class="adm-input">{{ old('description', $category?->description) }}</textarea>
</div>
<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="adm-label">Icon</label>
        <input type="text" name="icon" value="{{ old('icon', $category?->icon) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Color</label>
        <input type="text" name="color" value="{{ old('color', $category?->color) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Sort order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="adm-input">
    </div>
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Image</label>
        @if($category?->image)<img src="{{ img($category->image) }}" class="mb-2 h-16 w-16 rounded-lg object-cover">@endif
        <input type="file" name="image" accept="image/*" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Banner</label>
        @if($category?->banner)<img src="{{ img($category->banner) }}" class="mb-2 h-16 w-28 rounded-lg object-cover">@endif
        <input type="file" name="banner" accept="image/*" class="adm-input">
    </div>
</div>
<div>
    <label class="adm-label">Meta title</label>
    <input type="text" name="meta_title" value="{{ old('meta_title', $category?->meta_title) }}" class="adm-input">
</div>
<div>
    <label class="adm-label">Meta description</label>
    <textarea name="meta_description" rows="2" class="adm-input">{{ old('meta_description', $category?->meta_description) }}</textarea>
</div>
<div class="flex flex-wrap gap-4">
    <label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true))> Active</label>
    <label class="adm-checkbox"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $category?->is_featured))> Featured</label>
    <label class="adm-checkbox"><input type="checkbox" name="show_in_menu" value="1" @checked(old('show_in_menu', $category?->show_in_menu ?? true))> Show in menu</label>
</div>
