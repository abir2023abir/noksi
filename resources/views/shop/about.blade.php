<x-layouts.app :title="__('Our story')">
    <section class="relative min-h-[70vh] flex items-end overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=1600&q=80" alt="" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-plum-950 via-plum-950/50 to-transparent"></div>
        </div>
        <div class="container relative pb-16 md:pb-20 text-paper">
            <p class="eyebrow text-marigold-400">{{ __('Since 2019') }}</p>
            <h1 class="mt-3 font-display text-display-md md:text-display-lg text-balance max-w-2xl" data-split>{{ __('Made in Dhaka, worn everywhere') }}</h1>
            <p class="mt-4 text-lg text-paper/80 max-w-xl">{{ $page->excerpt ?? __('A clothing label born from a stubborn idea: that garments made here could be as considered as anything imported.') }}</p>
        </div>
    </section>

    <section class="container py-16 md:py-24">
        <div class="mx-auto max-w-2xl prose-nokshi prose-lg">
            {!! $page->body !!}
        </div>
    </section>

    <section class="bg-plum-950 text-paper py-16 md:py-20">
        <div class="container">
            <div class="grid gap-10 md:grid-cols-3 text-center">
                @foreach([
                    ['40+', __('Makers'), __('Weavers, tailors and embroiderers across Bangladesh')],
                    ['2019', __('Founded'), __('In a two-room flat in Lalmatia')],
                    ['100%', __('Small batch'), __('We make what we can sell')],
                ] as [$n, $t, $d])
                    <div class="reveal">
                        <p class="font-display text-5xl text-marigold-400">{{ $n }}</p>
                        <p class="mt-2 font-semibold text-lg">{{ $t }}</p>
                        <p class="mt-1 text-sm text-paper/60">{{ $d }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if($testimonials->isNotEmpty())
        <section class="container section">
            <x-section-heading eyebrow="{{ __('Voices') }}" title="{{ __('What people say about us') }}" align="center" />
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach($testimonials as $t)
                    <blockquote class="rounded-3xl bg-white p-6 ring-1 ring-ink/5">
                        <x-rating :value="$t->rating ?? 5" />
                        <p class="mt-4 text-ink-500 leading-relaxed">“{{ $t->body }}”</p>
                        <footer class="mt-5 flex items-center gap-3">
                            @if($t->avatar)
                                <img src="{{ img($t->avatar) }}" alt="" class="h-10 w-10 rounded-full object-cover">
                            @else
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-plum-100 font-bold text-plum-700">{{ mb_substr($t->name, 0, 1) }}</span>
                            @endif
                            <div>
                                <p class="font-semibold text-sm">{{ $t->name }}</p>
                                @if($t->role)<p class="text-xs text-ink-300">{{ $t->role }}</p>@endif
                            </div>
                        </footer>
                    </blockquote>
                @endforeach
            </div>
        </section>
    @endif

    <section class="container section-tight text-center">
        <h2 class="font-display text-display-sm">{{ __('Come see what we make') }}</h2>
        <p class="mt-3 text-ink-500 max-w-md mx-auto">{{ __('New drops every few weeks. No noise — just clothes worth wearing twice.') }}</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg mt-8" data-magnetic>{{ __('Shop the collection') }}</a>
    </section>
</x-layouts.app>
