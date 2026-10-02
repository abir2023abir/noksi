@props(['title' => null])
<aside class="lg:sticky lg:top-28 space-y-6">
    <div class="rounded-3xl bg-plum-950 text-paper p-6">
        <p class="text-sm text-paper/50">{{ __('Signed in as') }}</p>
        <p class="mt-1 font-display text-2xl truncate">{{ auth()->user()->name }}</p>
        <p class="mt-1 text-sm text-paper/60 truncate">{{ auth()->user()->email }}</p>
    </div>
    <nav class="rounded-3xl bg-white ring-1 ring-ink/5 p-2 space-y-0.5">
        @php
            $links = [
                ['account.dashboard', __('Overview'), 'home'],
                ['account.orders', __('Orders'), 'package'],
                ['account.wishlist', __('Wishlist'), 'heart'],
                ['account.addresses.index', __('Addresses'), 'map-pin'],
                ['account.reviews', __('Reviews'), 'star'],
                ['account.profile', __('Profile'), 'user'],
            ];
        @endphp
        @foreach($links as [$route, $label, $icon])
            <a href="{{ route($route) }}"
               class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs($route) || request()->routeIs($route.'.*') ? 'bg-plum-100 text-plum-900' : 'text-ink-500 hover:bg-paper hover:text-ink' }}">
                <x-icon :name="$icon" class="h-4.5 w-4.5" /> {{ $label }}
            </a>
        @endforeach
        <form method="POST" action="{{ route('logout') }}" class="pt-1 border-t border-ink/5 mt-1">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-ink-500 hover:bg-terracotta-50 hover:text-terracotta-700 transition">
                <x-icon name="logout" class="h-4.5 w-4.5" /> {{ __('Sign out') }}
            </button>
        </form>
    </nav>
</aside>
