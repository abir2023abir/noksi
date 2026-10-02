<x-layouts.app :title="__('Set new password')">
    <section class="container py-16 md:py-24">
        <div class="mx-auto max-w-md">
            <p class="eyebrow text-plum-700">{{ __('Account') }}</p>
            <h1 class="mt-2 font-display text-display-sm">{{ __('Choose a new password') }}</h1>

            <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5 rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label class="label">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" class="input" required>
                    @error('email')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">{{ __('New password') }}</label>
                    <input type="password" name="password" class="input" required minlength="8" autocomplete="new-password">
                    @error('password')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">{{ __('Confirm password') }}</label>
                    <input type="password" name="password_confirmation" class="input" required autocomplete="new-password">
                </div>
                <button class="btn btn-primary btn-lg w-full">{{ __('Update password') }}</button>
            </form>
        </div>
    </section>
</x-layouts.app>
