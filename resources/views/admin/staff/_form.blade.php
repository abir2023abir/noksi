@php $staff = $staff ?? null; @endphp
<div>
    <label class="adm-label">Name *</label>
    <input type="text" name="name" value="{{ old('name', $staff?->name) }}" class="adm-input" required>
    @error('name')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div>
    <label class="adm-label">Email *</label>
    <input type="email" name="email" value="{{ old('email', $staff?->email) }}" class="adm-input" required>
    @error('email')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div>
    <label class="adm-label">Phone</label>
    <input type="text" name="phone" value="{{ old('phone', $staff?->phone) }}" class="adm-input">
    @error('phone')<p class="adm-error">{{ $message }}</p>@enderror
</div>
<div>
    <label class="adm-label">Role *</label>
    <select name="role" class="adm-select" required>
        @foreach($roles as $role)
            <option value="{{ $role->name }}" @selected(old('role', $staff?->roles?->first()?->name) === $role->name)>{{ $role->name }}</option>
        @endforeach
    </select>
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="adm-label">{{ $staff ? 'New password' : 'Password *' }}</label>
        <input type="password" name="password" class="adm-input" @if(!$staff) required @endif autocomplete="new-password">
        @error('password')<p class="adm-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="adm-label">Confirm password</label>
        <input type="password" name="password_confirmation" class="adm-input" autocomplete="new-password">
    </div>
</div>
<div>
    <label class="adm-label">Avatar</label>
    @if($staff?->avatar)<img src="{{ img($staff->avatar) }}" class="mb-2 h-12 w-12 rounded-full object-cover">@endif
    <input type="file" name="avatar" accept="image/*" class="adm-input">
</div>
<label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $staff?->is_active ?? true))> Active</label>
