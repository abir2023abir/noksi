<x-layouts.app :title="__('Create account')">
    <section class="min-h-[calc(100vh-8rem)] grid lg:grid-cols-2">
        <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-teal-900 text-paper p-12 xl:p-16">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1200&q=80" alt="" class="h-full w-full object-cover opacity-45">
                <div class="absolute inset-0 bg-gradient-to-t from-teal-950 via-teal-900/75 to-teal-900/40"></div>
            </div>
            <a href="{{ route('home') }}" class="relative font-display text-3xl tracking-wide">NOKSHI</a>
            <div class="relative">
                <p class="eyebrow text-marigold-400">{{ __('Join us') }}</p>
                <h1 class="mt-3 font-display text-display-sm xl:text-display-md text-balance">{{ __('Wear the story from day one.') }}</h1>
                <p class="mt-4 text-paper/70 max-w-md">{{ __('Members get early access to drops, order tracking, and a wishlist that syncs across devices.') }}</p>
            </div>
        </div>

        <div class="flex items-center justify-center px-6 py-12 md:py-16 bg-paper">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-8"><a href="{{ route('home') }}" class="font-display text-2xl">NOKSHI</a></div>
                <h2 class="font-display text-3xl md:text-4xl">{{ __('Create account') }}</h2>
                <p class="mt-2 text-ink-500">{{ __('Already have one?') }} <a href="{{ route('login', ['redirect' => $redirect]) }}" class="font-semibold text-plum-700 hover:underline">{{ __('Sign in') }}</a></p>

                <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-4">
                    @csrf
                    @if($redirect)<input type="hidden" name="redirect" value="{{ $redirect }}">@endif
                    <div>
                        <label class="label">{{ __('Full name') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="input" required autocomplete="name">
                        @error('name')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="input" required autocomplete="email">
                        @error('email')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">{{ __('Mobile') }}</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="input" required placeholder="01XXXXXXXXX" autocomplete="tel">
                        @error('phone')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">{{ __('Password') }}</label>
                            <input type="password" name="password" class="input" required minlength="8" autocomplete="new-password">
                            @error('password')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">{{ __('Confirm') }}</label>
                            <input type="password" name="password_confirmation" class="input" required autocomplete="new-password">
                        </div>
                    </div>
                    <label class="flex items-start gap-2.5 text-sm cursor-pointer pt-1">
                        <input type="checkbox" name="terms" value="1" class="checkbox mt-0.5" required @checked(old('terms'))>
                        <span>{{ __('I agree to the') }} <a href="{{ route('page.show', 'terms') }}" class="font-semibold text-plum-700 hover:underline" target="_blank">{{ __('terms') }}</a>.</span>
                    </label>
                    @error('terms')<p class="text-xs text-terracotta-700">{{ $message }}</p>@enderror
                    <button class="btn btn-primary btn-lg w-full mt-2" data-magnetic>{{ __('Create account') }}</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
