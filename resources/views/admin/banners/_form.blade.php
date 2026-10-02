@php $banner = $banner ?? null; @endphp
<div>
    <label class="adm-label">Title</label>
    <input type="text" name="title" value="{{ old('title', $banner?->title) }}" class="adm-input">
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Subtitle</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $banner?->subtitle) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Eyebrow</label>
        <input type="text" name="eyebrow" value="{{ old('eyebrow', $banner?->eyebrow) }}" class="adm-input">
    </div>
</div>
<div>
    <label class="adm-label">Image {{ $banner ? '' : '*' }}</label>
    @if($banner?->image)<img src="{{ img($banner->image) }}" class="mb-2 h-24 w-40 rounded-lg object-cover">@endif
    <input type="file" name="image" accept="image/*" class="adm-input" @if(!$banner) required @endif>
    @error('image')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div>
    <label class="adm-label">Mobile image</label>
    @if($banner?->mobile_image)<img src="{{ img($banner->mobile_image) }}" class="mb-2 h-20 w-28 rounded-lg object-cover">@endif
    <input type="file" name="mobile_image" accept="image/*" class="adm-input">
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Button text</label>
        <input type="text" name="button_text" value="{{ old('button_text', $banner?->button_text) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Button URL</label>
        <input type="text" name="button_url" value="{{ old('button_url', $banner?->button_url) }}" class="adm-input">
    </div>
</div>
<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="adm-label">Position</label>
        <input type="text" name="position" value="{{ old('position', $banner?->position) }}" class="adm-input" placeholder="home_hero">
    </div>
    <div>
        <label class="adm-label">BG color</label>
        <input type="text" name="bg_color" value="{{ old('bg_color', $banner?->bg_color) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Text color</label>
        <input type="text" name="text_color" value="{{ old('text_color', $banner?->text_color) }}" class="adm-input">
    </div>
</div>
<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="adm-label">Sort</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Starts</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($banner?->starts_at)->format('Y-m-d\TH:i')) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Ends</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($banner?->ends_at)->format('Y-m-d\TH:i')) }}" class="adm-input">
    </div>
</div>
<label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner?->is_active ?? true))> Active</label>
