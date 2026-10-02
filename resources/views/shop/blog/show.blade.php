<x-layouts.app :title="$post->meta_title ?: $post->title" :metaDescription="$post->meta_description ?: $post->excerpt" :ogImage="$post->image_url">
    <article>
        <header class="relative overflow-hidden bg-plum-950 text-paper">
            <div class="absolute inset-0">
                <img src="{{ $post->image_url }}" alt="" class="h-full w-full object-cover opacity-40">
                <div class="absolute inset-0 bg-gradient-to-t from-plum-950 via-plum-950/80 to-plum-950/50"></div>
            </div>
            <div class="container relative py-16 md:py-24 max-w-3xl">
                <x-breadcrumb :items="[__('Journal') => route('blog.index'), $post->category?->name => $post->category ? route('blog.index', ['category' => $post->category->slug]) : null, $post->title => null]" class="text-paper/60 [&_a]:text-paper/60 [&_a:hover]:text-paper [&_span]:text-paper" />
                <p class="mt-6 eyebrow text-marigold-400">{{ $post->category?->name }}</p>
                <h1 class="mt-3 font-display text-display-sm md:text-display-md text-balance" data-split>{{ $post->title }}</h1>
                <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-paper/70">
                    @if($post->author)
                        <span class="inline-flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-paper/20 font-bold text-paper">{{ mb_substr($post->author->name, 0, 1) }}</span>
                            {{ $post->author->name }}
                        </span>
                    @endif
                    <span>{{ $post->published_at?->format('d M Y') }}</span>
                    <span>{{ $post->reading_time }} {{ __('min read') }}</span>
                </div>
            </div>
        </header>

        <div class="container py-12 md:py-16">
            <div class="mx-auto max-w-2xl prose-nokshi prose-lg">
                {!! $post->body !!}
            </div>

            @if($post->tags)
                <div class="mx-auto max-w-2xl mt-10 flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <span class="pill bg-white ring-ink/10">#{{ $tag }}</span>
                    @endforeach
                </div>
            @endif

            <div class="mx-auto max-w-2xl mt-10 flex items-center gap-3 text-sm text-ink-500">
                <span>{{ __('Share') }}</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="hover:text-ink"><x-icon name="facebook" class="h-5 w-5" /></a>
                <a href="https://wa.me/?text={{ urlencode($post->title.' '.url()->current()) }}" target="_blank" class="hover:text-ink"><x-icon name="whatsapp" class="h-5 w-5" /></a>
                <button type="button" class="hover:text-ink" @click="navigator.clipboard.writeText(location.href); $store.ui.toast(@js(__('Link copied')))"><x-icon name="copy" class="h-5 w-5" /></button>
            </div>
        </div>
    </article>

    @if($related->isNotEmpty())
        <section class="container section-tight border-t border-ink/5">
            <x-section-heading eyebrow="{{ __('Keep reading') }}" title="{{ __('More from the journal') }}" :link="route('blog.index')" />
            <div class="mt-10 grid gap-8 sm:grid-cols-3">
                @foreach($related as $r)
                    <a href="{{ route('blog.show', $r) }}" class="group">
                        <div class="aspect-[4/3] overflow-hidden rounded-3xl bg-paper-200">
                            <img src="{{ $r->image_url }}" alt="" class="h-full w-full object-cover transition group-hover:scale-105" loading="lazy">
                        </div>
                        <h3 class="mt-4 font-display text-xl group-hover:text-plum-700">{{ $r->title }}</h3>
                        <p class="mt-1 text-xs text-ink-300">{{ $r->published_at?->format('d M Y') }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if($shopPicks->isNotEmpty())
        <section class="container section-tight">
            <x-section-heading eyebrow="{{ __('Shop the story') }}" title="{{ __('Pieces we love right now') }}" :link="route('shop.index')" />
            <div class="mt-10 grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-4 md:gap-x-6">
                @foreach($shopPicks as $p)<x-product-card :product="$p" />@endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
