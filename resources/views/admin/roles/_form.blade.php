@php
    $role = $role ?? null;
    $selected = old('permissions', $role?->permissions?->pluck('name')->all() ?? []);
    $grouped = $permissions->groupBy(fn ($p) => explode('.', $p->name)[0] ?? 'other');
@endphp
<div>
    <label class="adm-label">Name *</label>
    <input type="text" name="name" value="{{ old('name', $role?->name) }}" class="adm-input" required @if($role?->name === 'super_admin') readonly @endif>
    @error('name')<p class="adm-error">{{ $message }}</p>@enderror
    @if($role?->name === 'super_admin')
        <p class="adm-hint">Super admin name is locked</p>
    @endif
</div>
<div>
    <div class="mb-3 flex items-center justify-between">
        <label class="adm-label !mb-0">Permissions</label>
        <button type="button" class="btn btn-ghost btn-sm" onclick="document.querySelectorAll('.perm-check').forEach(c => c.checked = true)">Select all</button>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($grouped as $group => $perms)
            <div class="rounded-xl border border-ink/10 p-4">
                <p class="mb-2 text-xs font-bold uppercase tracking-wider text-ink-300">{{ $group }}</p>
                <div class="space-y-2">
                    @foreach($perms as $perm)
                        <label class="adm-checkbox">
                            <input type="checkbox" class="perm-check" name="permissions[]" value="{{ $perm->name }}" @checked(in_array($perm->name, (array) $selected))>
                            {{ $perm->name }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @if(isset($known) && count(array_diff($known, $permissions->pluck('name')->all())))
        <p class="adm-hint mt-2">Some known permissions may not be seeded yet.</p>
    @endif
</div>
