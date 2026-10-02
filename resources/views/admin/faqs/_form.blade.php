@php $faq = $faq ?? null; @endphp
<div>
    <label class="adm-label">Category</label>
    <input type="text" name="category" value="{{ old('category', $faq?->category) }}" class="adm-input" placeholder="Shipping, Returns…">
</div>
<div>
    <label class="adm-label">Question *</label>
    <input type="text" name="question" value="{{ old('question', $faq?->question) }}" class="adm-input" required>
    @error('question')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div>
    <label class="adm-label">Answer *</label>
    <textarea name="answer" rows="6" class="adm-input" required>{{ old('answer', $faq?->answer) }}</textarea>
    @error('answer')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div>
    <label class="adm-label">Sort order</label>
    <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $faq?->sort_order ?? 0) }}" class="adm-input">
</div>
<label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq?->is_active ?? true))> Active</label>
