<div x-show="$store.ui.searchOpen" x-cloak class="fixed inset-0 z-[60]" role="dialog" aria-modal="true" x-data="liveSearch()">
    <div x-show="$store.ui.searchOpen" x-transition.opacity.duration.300ms class="absolute inset-0 bg-plum-950/70 backdrop-blur-sm" @click="$store.ui.searchOpen = false"></div>

    <div x-show="$store.ui.searchOpen"
         x-transition:enter="transition ease-out-expo duration-500" x-transition:enter-start="opacity-0 -translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition duration-200" x-transition:leave-end="opacity-0 -translate-y-4"
         class="absolute inset-x-0 top-0 bg-paper shadow-lift max-h-[90vh] overflow-y-auto"
         x-trap.noscroll="$store.ui.searchOpen" @keydown.escape.window="$store.ui.searchOpen = false"
         x-effect="if ($store.ui.searchOpen) setTimeout(() => $refs.input?.focus(), 50)">
        <div class="container py-8 md:py-12">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <x-icon name="search" class="absolute left-0 top-1/2 -translate-y-1/2 h-7 w-7 text-ink-300" />
                <input x-ref="input" type="search" name="q" x-model="q" @input="search()" autocomplete="off"
                       placeholder="{{ __('Search sarees, panjabis, tees…') }}"
                       class="w-full border-0 border-b-2 border-ink/10 bg-transparent pl-11 pr-14 py-4 font-display text-3xl md:text-5xl text-ink placeholder:text-ink-300/60 focus:border-plum-700 focus:outline-none focus:ring-0">
                <button type="button" class="icon-btn absolute right-0 top-1/2 -translate-y-1/2" @click="$store.ui.searchOpen = false" aria-label="{{ __('Close') }}"><x-icon name="x" class="h-6 w-6" /></button>
            </form>

            <div class="mt-8 grid gap-8 md:grid-cols-12">
                <div class="md:col-span-3">
                    <p class="eyebrow text-ink-300 mb-3">{{ __('Popular') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Jamdani', 'Panjabi', 'Linen', 'Kurti', 'Tee', 'Denim', 'Eid'] as $tag)
                            <a href="{{ route('search', ['q' => $tag]) }}" class="pill ring-ink/10 bg-white hover:bg-plum-950 hover:text-paper transition">{{ $tag }}</a>
                        @endforeach
                    </div>
                    <template x-if="results.categories.length">
                        <div class="mt-6">
                            <p class="eyebrow text-ink-300 mb-3">{{ __('Categories') }}</p>
                            <ul class="space-y-1.5">
                                <template x-for="c in results.categories" :key="c.url">
                                    <li><a :href="c.url" class="link-underline font-medium" x-text="c.name"></a></li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>
                <div class="md:col-span-9">
                    <template x-if="loading">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <template x-for="i in 3"><div class="skeleton aspect-[4/5] rounded-2xl"></div></template>
                        </div>
                    </template>
                    <template x-if="!loading && q.length >= 2 && results.products.length === 0">
                        <p class="text-ink-500 py-8">{{ __('No matches for') }} “<span x-text="q"></span>”. {{ __('Try a different word.') }}</p>
                    </template>
                    <template x-if="!loading && results.products.length">
                        <div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <template x-for="p in results.products" :key="p.url">
                                    <a :href="p.url" class="group flex gap-3 rounded-2xl bg-white p-2 ring-1 ring-ink/5 hover:ring-plum-700/30 transition">
                                        <img :src="p.image" :alt="p.name" class="h-20 w-16 shrink-0 rounded-xl object-cover">
                                        <div class="min-w-0 py-1">
                                            <p class="text-xs text-ink-300" x-text="p.category"></p>
                                            <p class="truncate font-semibold text-sm group-hover:text-plum-700" x-text="p.name"></p>
                                            <p class="text-sm font-bold mt-1" x-text="p.price"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                            <a :href="'{{ route('search') }}?q=' + encodeURIComponent(q)" class="mt-5 inline-flex items-center gap-1 font-semibold text-plum-700">{{ __('See all results') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
