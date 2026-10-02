@php $flashSale = $flashSale ?? null; @endphp
<div>
    <label class="adm-label">Title *</label>
    <input type="text" name="title" value="{{ old('title', $flashSale?->title) }}" class="adm-input" required>
    @error('title')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Subtitle</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $flashSale?->subtitle) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $flashSale?->slug) }}" class="adm-input" placeholder="auto">
    </div>
</div>
<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="adm-label">Starts at *</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($flashSale?->starts_at)->format('Y-m-d\TH:i')) }}" class="adm-input" required>
    </div>
    <div>
        <label class="adm-label">Ends at *</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($flashSale?->ends_at)->format('Y-m-d\TH:i')) }}" class="adm-input" required>
    </div>
    <div>
        <label class="adm-label">Color</label>
        <input type="text" name="color" value="{{ old('color', $flashSale?->color) }}" class="adm-input">
    </div>
</div>
<div>
    <label class="adm-label">Image</label>
    @if($flashSale?->image)<img src="{{ img($flashSale->image) }}" class="mb-2 h-24 w-40 rounded-lg object-cover">@endif
    <input type="file" name="image" accept="image/*" class="adm-input">
</div>
<label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $flashSale?->is_active ?? true))> Active</label>
