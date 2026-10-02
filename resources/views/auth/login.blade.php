<x-layouts.app :title="__('Sign in')">
    <section class="min-h-[calc(100vh-8rem)] grid lg:grid-cols-2">
        <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-plum-950 text-paper p-12 xl:p-16">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1200&q=80" alt="" class="h-full w-full object-cover opacity-50">
                <div class="absolute inset-0 bg-gradient-to-t from-plum-950 via-plum-950/70 to-plum-950/40"></div>
            </div>
            <a href="{{ route('home') }}" class="relative font-display text-3xl tracking-wide">NOKSHI</a>
            <div class="relative">
                <p class="eyebrow text-marigold-400">{{ __('Welcome back') }}</p>
                <h1 class="mt-3 font-display text-display-sm xl:text-display-md text-balance">{{ __('Your wardrobe is waiting.') }}</h1>
                <p class="mt-4 text-paper/70 max-w-md">{{ __('Track orders, save favourites, and checkout faster next time.') }}</p>
            </div>
        </div>

        <div class="flex items-center justify-center px-6 py-12 md:py-16 bg-paper">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-8">
                    <a href="{{ route('home') }}" class="font-display text-2xl">NOKSHI</a>
                </div>
                <h2 class="font-display text-3xl md:text-4xl">{{ __('Sign in') }}</h2>
                <p class="mt-2 text-ink-500">{{ __('New here?') }} <a href="{{ route('register', ['redirect' => $redirect]) }}" class="font-semibold text-plum-700 hover:underline">{{ __('Create an account') }}</a></p>

                @if(session('success'))
                    <div class="mt-6"><x-alert type="success">{{ session('success') }}</x-alert></div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                    @csrf
                    @if($redirect)<input type="hidden" name="redirect" value="{{ $redirect }}">@endif
                    <div>
                        <label class="label">{{ __('Email or mobile') }}</label>
                        <input type="text" name="email" value="{{ old('email') }}" class="input" required autofocus autocomplete="username" placeholder="you@email.com / 01XXXXXXXXX">
                        @error('email')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="label !mb-0">{{ __('Password') }}</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-plum-700 hover:underline">{{ __('Forgot?') }}</a>
                        </div>
                        <input type="password" name="password" class="input" required autocomplete="current-password">
                    </div>
                    <label class="flex items-center gap-2.5 text-sm cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="checkbox">
                        <span>{{ __('Keep me signed in') }}</span>
                    </label>
                    <button class="btn btn-primary btn-lg w-full" data-magnetic>{{ __('Sign in') }}</button>
                </form>

                <p class="mt-8 text-center text-xs text-ink-300">{{ __('Demo: customer@nokshi.test / password') }}</p>
            </div>
        </div>
    </section>
</x-layouts.app>
