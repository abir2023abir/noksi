@php $brand = $brand ?? null; @endphp
<div>
    <label class="adm-label">Name *</label>
    <input type="text" name="name" value="{{ old('name', $brand?->name) }}" class="adm-input" required>
    @error('name')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div>
    <label class="adm-label">Slug</label>
    <input type="text" name="slug" value="{{ old('slug', $brand?->slug) }}" class="adm-input" placeholder="auto">
</div>
<div>
    <label class="adm-label">Description</label>
    <textarea name="description" rows="3" class="adm-input">{{ old('description', $brand?->description) }}</textarea>
</div>
<div>
    <label class="adm-label">Website</label>
    <input type="url" name="website" value="{{ old('website', $brand?->website) }}" class="adm-input" placeholder="https://">
    @error('website')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div>
    <label class="adm-label">Logo</label>
    @if($brand?->logo)<img src="{{ img($brand->logo) }}" class="mb-2 h-14 w-14 rounded-lg object-contain bg-paper-100">@endif
    <input type="file" name="logo" accept="image/*" class="adm-input">
</div>
<div class="flex flex-wrap gap-4">
    <label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $brand?->is_active ?? true))> Active</label>
    <label class="adm-checkbox"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $brand?->is_featured))> Featured</label>
</div>
