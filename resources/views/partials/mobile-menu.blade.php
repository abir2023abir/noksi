<div x-show="$store.ui.menuOpen" x-cloak class="fixed inset-0 z-[60] lg:hidden" role="dialog" aria-modal="true">
    <div x-show="$store.ui.menuOpen" x-transition.opacity.duration.300ms class="absolute inset-0 bg-plum-950/60 backdrop-blur-sm" @click="$store.ui.menuOpen = false"></div>

    <aside x-show="$store.ui.menuOpen"
           x-transition:enter="transition ease-out-expo duration-500" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out-quart duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
           class="absolute left-0 top-0 h-full w-full max-w-sm flex flex-col bg-paper shadow-lift overflow-y-auto"
           x-trap.noscroll="$store.ui.menuOpen" @keydown.escape.window="$store.ui.menuOpen = false">

        <div class="flex items-center justify-between px-5 py-4 border-b border-ink/5">
            <span class="font-display text-2xl font-semibold">NOKSHI</span>
            <button class="icon-btn -mr-2" @click="$store.ui.menuOpen = false" aria-label="{{ __('Close') }}"><x-icon name="x" class="h-6 w-6" /></button>
        </div>

        <nav class="flex-1 px-3 py-4" x-data="{ open: null }">
            @foreach($menuCategories as $cat)
                <div class="border-b border-ink/5">
                    <button class="flex w-full items-center justify-between px-3 py-4 text-left font-display text-2xl" @click="open = open === {{ $cat->id }} ? null : {{ $cat->id }}">
                        <span>{{ $cat->name }}</span>
                        @if($cat->children->isNotEmpty())
                            <x-icon name="chevron-down" class="h-5 w-5 text-ink-300 transition" ::class="{ 'rotate-180': open === {{ $cat->id }} }" />
                        @endif
                    </button>
                    @if($cat->children->isNotEmpty())
                        <div x-show="open === {{ $cat->id }}" x-collapse x-cloak>
                            <ul class="pb-4 pl-3 space-y-1">
                                <li><a href="{{ $cat->url }}" class="block rounded-xl px-3 py-2 font-semibold text-plum-700">{{ __('All :name', ['name' => $cat->name]) }}</a></li>
                                @foreach($cat->children as $child)
                                    <li><a href="{{ $child->url }}" class="block rounded-xl px-3 py-2 text-[15px] font-medium text-ink-700 hover:bg-white">{{ $child->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
            <a href="{{ route('shop.new') }}" class="flex items-center justify-between border-b border-ink/5 px-3 py-4 font-display text-2xl">{{ __('New') }} <span class="badge bg-marigold-400 text-plum-950">{{ __('Fresh') }}</span></a>
            <a href="{{ route('shop.sale') }}" class="flex items-center justify-between border-b border-ink/5 px-3 py-4 font-display text-2xl text-terracotta-700">{{ __('Sale') }}</a>
            <a href="{{ route('blog.index') }}" class="flex items-center justify-between border-b border-ink/5 px-3 py-4 font-display text-2xl">{{ __('Journal') }}</a>

            <div class="mt-6 grid grid-cols-2 gap-2 px-1">
                <a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}" class="btn btn-outline btn-md"><x-icon name="user" class="h-4.5 w-4.5" /> {{ auth()->check() ? __('Account') : __('Sign in') }}</a>
                <a href="{{ route('wishlist.index') }}" class="btn btn-outline btn-md"><x-icon name="heart" class="h-4.5 w-4.5" /> {{ __('Wishlist') }}</a>
            </div>
            <div class="mt-4 flex items-center justify-center gap-4 text-sm font-semibold">
                <a href="{{ route('locale', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'text-plum-700 underline' : 'text-ink-500' }}">English</a>
                <span class="text-ink-300">·</span>
                <a href="{{ route('locale', 'bn') }}" class="font-bangla {{ app()->getLocale() === 'bn' ? 'text-plum-700 underline' : 'text-ink-500' }}">বাংলা</a>
            </div>
        </nav>

        <div class="border-t border-ink/5 px-5 py-4 text-sm text-ink-500">
            <p>{{ setting('contact_phone') }}</p>
            <p>{{ setting('business_hours') }}</p>
        </div>
    </aside>
</div>
