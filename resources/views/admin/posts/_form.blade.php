@php
    $post = $post ?? null;
    $tagsValue = old('tags', is_array($post?->tags) ? implode(', ', $post->tags) : ($post?->tags ?? ''));
@endphp
<div>
    <label class="adm-label">Title *</label>
    <input type="text" name="title" value="{{ old('title', $post?->title) }}" class="adm-input" required>
    @error('title')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $post?->slug) }}" class="adm-input" placeholder="auto">
    </div>
    <div>
        <label class="adm-label">Category</label>
        <select name="blog_category_id" class="adm-select">
            <option value="">—</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('blog_category_id', $post?->blog_category_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div>
    <label class="adm-label">Excerpt</label>
    <textarea name="excerpt" rows="2" class="adm-input">{{ old('excerpt', $post?->excerpt) }}</textarea>
</div>
<div>
    <label class="adm-label">Body *</label>
    <textarea name="body" rows="12" class="adm-input font-mono text-sm" required>{{ old('body', $post?->body) }}</textarea>
    @error('body')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="adm-label">Status *</label>
        <select name="status" class="adm-select" required>
            <option value="draft" @selected(old('status', $post?->status ?? 'draft')==='draft')>Draft</option>
            <option value="published" @selected(old('status', $post?->status)==='published')>Published</option>
        </select>
    </div>
    <div>
        <label class="adm-label">Published at</label>
        <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($post?->published_at)->format('Y-m-d\TH:i')) }}" class="adm-input">
    </div>
    <div class="flex items-end pb-2">
        <label class="adm-checkbox"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post?->is_featured))> Featured</label>
    </div>
</div>
<div>
    <label class="adm-label">Cover image</label>
    @if($post?->image)<img src="{{ img($post->image) }}" class="mb-2 h-28 w-44 rounded-lg object-cover">@endif
    <input type="file" name="image" accept="image/*" class="adm-input">
</div>
<div>
    <label class="adm-label">Tags</label>
    <input type="text" name="tags" value="{{ $tagsValue }}" class="adm-input" placeholder="comma, separated">
</div>
<div>
    <label class="adm-label">Meta title</label>
    <input type="text" name="meta_title" value="{{ old('meta_title', $post?->meta_title) }}" class="adm-input">
</div>
<div>
    <label class="adm-label">Meta description</label>
    <textarea name="meta_description" rows="2" class="adm-input">{{ old('meta_description', $post?->meta_description) }}</textarea>
</div>
