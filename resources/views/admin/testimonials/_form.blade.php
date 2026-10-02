@php $testimonial = $testimonial ?? null; @endphp
<div>
    <label class="adm-label">Name *</label>
    <input type="text" name="name" value="{{ old('name', $testimonial?->name) }}" class="adm-input" required>
    @error('name')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Role</label>
        <input type="text" name="role" value="{{ old('role', $testimonial?->role) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Rating</label>
        <input type="number" min="1" max="5" name="rating" value="{{ old('rating', $testimonial?->rating ?? 5) }}" class="adm-input">
    </div>
</div>
<div>
    <label class="adm-label">Body *</label>
    <textarea name="body" rows="4" class="adm-input" required>{{ old('body', $testimonial?->body) }}</textarea>
    @error('body')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Sort</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $testimonial?->sort_order ?? 0) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Avatar</label>
        @if($testimonial?->avatar)<img src="{{ img($testimonial->avatar) }}" class="mb-2 h-12 w-12 rounded-full object-cover">@endif
        <input type="file" name="avatar" accept="image/*" class="adm-input">
    </div>
</div>
<label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $testimonial?->is_active ?? true))> Active</label>
