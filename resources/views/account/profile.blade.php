<x-layouts.app :title="__('Profile')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-10 md:py-12">
            <h1 class="font-display text-display-sm">{{ __('Profile') }}</h1>
            <p class="mt-2 text-paper/70">{{ __('Your details, kept up to date.') }}</p>
        </div>
    </section>

    <section class="container py-10 md:py-14">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-3">@include('partials.account-nav')</div>
            <div class="lg:col-span-9 space-y-8">
                <form method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data" class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5 space-y-5">
                    @csrf @method('PUT')
                    <h2 class="font-display text-2xl">{{ __('Personal info') }}</h2>
                    <div class="flex items-center gap-5">
                        <div class="h-20 w-20 overflow-hidden rounded-full bg-plum-100 flex items-center justify-center text-2xl font-bold text-plum-700 shrink-0">
                            @if($user->avatar)
                                <img src="{{ img($user->avatar) }}" alt="" class="h-full w-full object-cover">
                            @else
                                {{ mb_substr($user->name, 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <label class="btn btn-outline btn-sm cursor-pointer">
                                {{ __('Change photo') }}
                                <input type="file" name="avatar" accept="image/*" class="sr-only" onchange="this.form.requestSubmit()">
                            </label>
                            <p class="mt-1.5 text-xs text-ink-300">JPG or PNG, max 2MB</p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="label">{{ __('Full name') }}</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input" required>
                            @error('name')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Email') }}</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input" required>
                            @error('email')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Phone') }}</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="input">
                            @error('phone')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Date of birth') }}</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" class="input">
                        </div>
                        <div>
                            <label class="label">{{ __('Gender') }}</label>
                            <select name="gender" class="input">
                                <option value="">{{ __('Prefer not to say') }}</option>
                                @foreach(['female','male','other'] as $g)
                                    <option value="{{ $g }}" @selected(old('gender', $user->gender) === $g)>{{ __(ucfirst($g)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-md">{{ __('Save changes') }}</button>
                </form>

                <form method="POST" action="{{ route('account.password') }}" class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5 space-y-5">
                    @csrf @method('PUT')
                    <h2 class="font-display text-2xl">{{ __('Change password') }}</h2>
                    <div>
                        <label class="label">{{ __('Current password') }}</label>
                        <input type="password" name="current_password" class="input" required autocomplete="current-password">
                        @error('current_password')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">{{ __('New password') }}</label>
                            <input type="password" name="password" class="input" required minlength="8" autocomplete="new-password">
                            @error('password')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Confirm') }}</label>
                            <input type="password" name="password_confirmation" class="input" required autocomplete="new-password">
                        </div>
                    </div>
                    <button class="btn btn-outline btn-md">{{ __('Update password') }}</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
