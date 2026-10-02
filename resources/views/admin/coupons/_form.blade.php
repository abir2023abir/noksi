@php $coupon = $coupon ?? null; @endphp
<div>
    <label class="adm-label">Code *</label>
    <input type="text" name="code" value="{{ old('code', $coupon?->code) }}" class="adm-input font-mono uppercase" required>
    @error('code')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div>
    <label class="adm-label">Description</label>
    <input type="text" name="description" value="{{ old('description', $coupon?->description) }}" class="adm-input">
</div>
<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="adm-label">Type *</label>
        <select name="type" class="adm-select" required>
            @foreach(['fixed','percent','free_shipping'] as $t)
                <option value="{{ $t }}" @selected(old('type', $coupon?->type) === $t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="adm-label">Value *</label>
        <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon?->value ?? 0) }}" class="adm-input" required>
    </div>
    <div>
        <label class="adm-label">Min order</label>
        <input type="number" step="0.01" min="0" name="min_order" value="{{ old('min_order', $coupon?->min_order) }}" class="adm-input">
    </div>
</div>
<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="adm-label">Max discount</label>
        <input type="number" step="0.01" min="0" name="max_discount" value="{{ old('max_discount', $coupon?->max_discount) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Usage limit</label>
        <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon?->usage_limit) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Per user limit</label>
        <input type="number" min="1" name="per_user_limit" value="{{ old('per_user_limit', $coupon?->per_user_limit) }}" class="adm-input">
    </div>
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">Starts at</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($coupon?->starts_at)->format('Y-m-d\TH:i')) }}" class="adm-input">
    </div>
    <div>
        <label class="adm-label">Expires at</label>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($coupon?->expires_at)->format('Y-m-d\TH:i')) }}" class="adm-input">
    </div>
</div>
<label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon?->is_active ?? true))> Active</label>
