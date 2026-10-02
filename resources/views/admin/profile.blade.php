@extends('admin.layouts.app')

@section('title', 'Profile')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">Profile</h1>
        <p class="mt-1 text-sm text-ink-500">Update your account details</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="adm-card max-w-2xl">
    @csrf
    @method('PUT')
    <div class="space-y-5 p-6">
        <div class="flex items-center gap-4">
            <img src="{{ img($user->avatar, 'images/avatar.svg') }}" alt="" class="h-16 w-16 rounded-full object-cover bg-paper-100">
            <div class="flex-1">
                <label class="adm-label">Avatar</label>
                <input type="file" name="avatar" accept="image/*" class="adm-input">
                @error('avatar')<p class="adm-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="adm-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="adm-input" required>
            @error('name')<p class="adm-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="adm-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="adm-input" required>
            @error('email')<p class="adm-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="adm-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="adm-input">
            @error('phone')<p class="adm-error">{{ $message }}</p>@enderror
        </div>
        <hr class="border-ink/5">
        <p class="text-sm font-semibold text-ink-500">Change password</p>
        <div>
            <label class="adm-label">Current password</label>
            <input type="password" name="current_password" class="adm-input" autocomplete="current-password">
            @error('current_password')<p class="adm-error">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="adm-label">New password</label>
                <input type="password" name="password" class="adm-input" autocomplete="new-password">
                @error('password')<p class="adm-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="adm-label">Confirm password</label>
                <input type="password" name="password_confirmation" class="adm-input" autocomplete="new-password">
            </div>
        </div>
        <div class="flex justify-end">
            <button class="btn btn-primary">Save profile</button>
        </div>
    </div>
</form>
@endsection
