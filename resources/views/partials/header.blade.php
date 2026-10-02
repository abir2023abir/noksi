@php $announcement = setting('announcement_active', '1') === '1' ? setting('announcement_text') : null; @endphp

@if($announcement)
    <div class="relative z-50 bg-plum-950 text-marigold-400 text-[13px] font-medium">
        <div class="marquee py-2">
            <div class="marquee__track">
                @for($i = 0; $i < 6; $i++)
                    <span class="flex items-center gap-8">
                        <span>{{ $announcement }}</span>
                        <span class="h-1.5 w-1.5 rounded-full bg-marigold-400/60"></span>
                    </span>
                @endfor
            </div>
        </div>
    </div>
@endif

<header id="site-header"
        class="sticky top-0 z-40 transition-all duration-500 ease-out-expo bg-paper/95 backdrop-blur-md border-b border-ink/5 [&.is-scrolled]:shadow-soft [&.is-hidden]:-translate-y-full"
        x-data="{ mega: null }" @mouseleave="mega = null">
    <div class="container">
        <div class="flex h-[76px] items-center justify-between gap-6">

            {{-- Left: mobile menu + nav --}}
            <div class="flex items-center gap-2 lg:gap-8">
                <button class="icon-btn lg:hidden -ml-2" @click="$store.ui.menuOpen = true" aria-label="Menu">
                    <x-icon name="menu" class="h-6 w-6" />
                </button>

                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group" aria-label="{{ setting('site_name') }}">
                    @if(setting('logo'))
                        <img src="{{ img(setting('logo')) }}" alt="{{ setting('site_name') }}" class="h-9 w-auto">
                    @else
                        <span class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-plum-950 text-marigold-400 transition-transform duration-500 ease-out-expo group-hover:rotate-[-8deg]">
                            <span class="font-bangla text-xl font-bold leading-none">ন</span>
                            <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-terracotta-500 ring-2 ring-paper"></span>
                        </span>
                        <span class="font-display text-[26px] font-semibold tracking-tight text-ink">NOKSHI</span>
                    @endif
                </a>

                <nav class="hidden lg:flex items-center gap-1 ml-4" aria-label="Primary">
                    @foreach($menuCategories as $cat)
                        <div class="relative" @mouseenter="mega = {{ $cat->id }}">
                            <a href="{{ $cat->url }}"
                               class="flex items-center gap-1 rounded-full px-4 py-2 text-[15px] font-semibold text-ink-700 transition hover:bg-ink/5 hover:text-ink"
                               :class="{ 'bg-ink/5 text-ink': mega === {{ $cat->id }} }">
                                {{ app()->getLocale() === 'bn' && $cat->name_bn ? $cat->name_bn : $cat->name }}
                                @if($cat->children->isNotEmpty())
                                    <x-icon name="chevron-down" class="h-3.5 w-3.5 opacity-60 transition" ::class="{ 'rotate-180': mega === {{ $cat->id }} }" />
                                @endif
                            </a>
                        </div>
                    @endforeach
                    <a href="{{ route('shop.new') }}" class="rounded-full px-4 py-2 text-[15px] font-semibold text-ink-700 transition hover:bg-ink/5 hover:text-ink" @mouseenter="mega = null">{{ __('New') }}</a>
                    <a href="{{ route('shop.sale') }}" class="rounded-full px-4 py-2 text-[15px] font-semibold text-terracotta-700 transition hover:bg-terracotta-50" @mouseenter="mega = null">{{ __('Sale') }}</a>
                    <a href="{{ route('blog.index') }}" class="rounded-full px-4 py-2 text-[15px] font-semibold text-ink-700 transition hover:bg-ink/5 hover:text-ink" @mouseenter="mega = null">{{ __('Journal') }}</a>
                </nav>
            </div>

            {{-- Right: actions --}}
            <div class="flex items-center gap-0.5 sm:gap-1">
                <button class="icon-btn" @click="$store.ui.searchOpen = true" aria-label="{{ __('Search') }}">
                    <x-icon name="search" class="h-[22px] w-[22px]" />
                </button>

                <div class="hidden sm:block relative" x-data="{ open: false }" @click.outside="open = false">
                    <button class="icon-btn text-xs font-bold uppercase tracking-wider" @click="open = !open" aria-label="{{ __('Language') }}">
                        {{ app()->getLocale() === 'bn' ? 'বাং' : 'EN' }}
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right class="absolute right-0 mt-2 w-36 overflow-hidden rounded-2xl bg-white p-1.5 shadow-lift ring-1 ring-ink/5">
                        <a href="{{ route('locale', 'en') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium hover:bg-paper {{ app()->getLocale() === 'en' ? 'text-plum-700' : '' }}">English @if(app()->getLocale() === 'en')<x-icon name="check" class="h-4 w-4" />@endif</a>
                        <a href="{{ route('locale', 'bn') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium hover:bg-paper font-bangla {{ app()->getLocale() === 'bn' ? 'text-plum-700' : '' }}">বাংলা @if(app()->getLocale() === 'bn')<x-icon name="check" class="h-4 w-4" />@endif</a>
                    </div>
                </div>

                <a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}" class="icon-btn hidden sm:inline-flex" aria-label="{{ __('Account') }}">
                    @auth
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-plum-100 text-xs font-bold text-plum-700">{{ auth()->user()->initials }}</span>
                    @else
                        <x-icon name="user" class="h-[22px] w-[22px]" />
                    @endauth
                </a>

                <a href="{{ route('wishlist.index') }}" class="icon-btn hidden sm:inline-flex" aria-label="{{ __('Wishlist') }}">
                    <x-icon name="heart" class="h-[22px] w-[22px]" />
                    <span x-show="$store.wishlist.ids.length" x-cloak x-text="$store.wishlist.ids.length" class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-terracotta-500 px-1 text-[10px] font-bold text-white"></span>
                </a>

                <button class="icon-btn" @click="$store.ui.cartOpen = true" aria-label="{{ __('Bag') }}">
                    <x-icon name="bag" class="h-[22px] w-[22px]" />
                    <span x-show="$store.cart.count > 0" x-cloak x-text="$store.cart.count"
                          class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-plum-950 px-1 text-[10px] font-bold text-marigold-400 transition-transform"
                          :class="{ 'scale-125': $store.cart.bump }"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Mega menu --}}
    @foreach($menuCategories as $cat)
        @if($cat->children->isNotEmpty())
            <div x-show="mega === {{ $cat->id }}" x-cloak
                 x-transition:enter="transition ease-out-expo duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition duration-150" x-transition:leave-end="opacity-0"
                 class="absolute inset-x-0 top-full hidden lg:block border-t border-ink/5 bg-paper shadow-lift">
                <div class="container grid grid-cols-12 gap-8 py-8">
                    <div class="col-span-3">
                        <p class="eyebrow text-ink-300 mb-3">{{ __('Shop :name', ['name' => $cat->name]) }}</p>
                        <ul class="space-y-1.5">
                            @foreach($cat->children as $child)
                                <li><a href="{{ $child->url }}" class="link-underline text-[15px] font-medium text-ink-700 hover:text-ink">{{ app()->getLocale() === 'bn' && $child->name_bn ? $child->name_bn : $child->name }}</a></li>
                            @endforeach
                            <li class="pt-2"><a href="{{ $cat->url }}" class="inline-flex items-center gap-1 text-sm font-bold text-plum-700">{{ __('View all') }} <x-icon name="arrow-right" class="h-4 w-4" /></a></li>
                        </ul>
                    </div>
                    <div class="col-span-3">
                        <p class="eyebrow text-ink-300 mb-3">{{ __('Quick links') }}</p>
                        <ul class="space-y-1.5 text-[15px] font-medium text-ink-700">
                            <li><a href="{{ route('shop.new') }}" class="link-underline hover:text-ink">{{ __('New arrivals') }}</a></li>
                            <li><a href="{{ route('shop.sale') }}" class="link-underline hover:text-ink">{{ __('On sale') }}</a></li>
                            <li><a href="{{ route('shop.index', ['sort' => 'popular']) }}" class="link-underline hover:text-ink">{{ __('Bestsellers') }}</a></li>
                            <li><a href="{{ route('page.show', 'size-guide') }}" class="link-underline hover:text-ink">{{ __('Size guide') }}</a></li>
                        </ul>
                    </div>
                    <div class="col-span-6 grid grid-cols-2 gap-4">
                        @foreach($cat->children->take(2) as $child)
                            <a href="{{ $child->url }}" class="group relative aspect-[16/10] overflow-hidden rounded-3xl bg-paper-200">
                                <img src="{{ $child->image_url }}" alt="{{ $child->name }}" class="h-full w-full object-cover transition duration-700 ease-out-expo group-hover:scale-105" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-ink/10 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between text-paper">
                                    <span class="font-display text-2xl">{{ $child->name }}</span>
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-paper text-ink transition group-hover:bg-marigold-400"><x-icon name="arrow-up-right" class="h-4 w-4" /></span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</header>
