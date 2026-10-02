<x-layouts.app :title="__('Forgot password')">
    <section class="container py-16 md:py-24">
        <div class="mx-auto max-w-md">
            <p class="eyebrow text-plum-700">{{ __('Account') }}</p>
            <h1 class="mt-2 font-display text-display-sm">{{ __('Reset your password') }}</h1>
            <p class="mt-3 text-ink-500">{{ __('Enter the email on your account. If it exists, we\'ll send a reset link.') }}</p>

            @if(session('success'))
                <div class="mt-6"><x-alert type="success">{{ session('success') }}</x-alert></div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5 rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5">
                @csrf
                <div>
                    <label class="label">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input" required autofocus>
                    @error('email')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                </div>
                <button class="btn btn-primary btn-lg w-full">{{ __('Send reset link') }}</button>
                <p class="text-center text-sm"><a href="{{ route('login') }}" class="font-semibold text-plum-700 hover:underline">{{ __('Back to sign in') }}</a></p>
            </form>
        </div>
    </section>
</x-layouts.app>
