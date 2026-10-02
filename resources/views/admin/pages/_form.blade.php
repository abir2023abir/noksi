@php $page = $page ?? null; @endphp
<div>
    <label class="adm-label">Title *</label>
    <input type="text" name="title" value="{{ old('title', $page?->title) }}" class="adm-input" required>
    @error('title')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $page?->slug) }}" class="adm-input" placeholder="auto">
    </div>
    <div>
        <label class="adm-label">Template</label>
        <input type="text" name="template" value="{{ old('template', $page?->template) }}" class="adm-input" placeholder="default">
    </div>
</div>
<div>
    <label class="adm-label">Body</label>
    <textarea name="body" rows="12" class="adm-input font-mono text-sm">{{ old('body', $page?->body) }}</textarea>
</div>
<div>
    <label class="adm-label">Meta title</label>
    <input type="text" name="meta_title" value="{{ old('meta_title', $page?->meta_title) }}" class="adm-input">
</div>
<div>
    <label class="adm-label">Meta description</label>
    <textarea name="meta_description" rows="2" class="adm-input">{{ old('meta_description', $page?->meta_description) }}</textarea>
</div>
<div class="flex flex-wrap gap-4">
    <label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $page?->is_active ?? true))> Active</label>
    <label class="adm-checkbox"><input type="checkbox" name="show_in_footer" value="1" @checked(old('show_in_footer', $page?->show_in_footer ?? true))> Show in footer</label>
</div>
