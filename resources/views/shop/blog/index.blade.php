<x-layouts.app :title="__('Journal')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-12 md:py-16">
            <x-breadcrumb :items="[__('Journal') => null]" class="text-paper/60 [&_a]:text-paper/60 [&_a:hover]:text-paper [&_span]:text-paper" />
            <h1 class="mt-4 font-display text-display-sm md:text-display-md" data-split>{{ __('The Journal') }}</h1>
            <p class="mt-3 text-paper/70 max-w-xl">{{ __('Stories from the loom, the cutting table, and the streets of Dhaka.') }}</p>
        </div>
    </section>

    @if($featured)
        <section class="container py-10 md:py-14">
            <a href="{{ route('blog.show', $featured) }}" class="group grid gap-8 lg:grid-cols-2 items-center rounded-4xl overflow-hidden bg-white ring-1 ring-ink/5">
                <div class="aspect-[4/3] lg:aspect-auto lg:min-h-[420px] overflow-hidden">
                    <img src="{{ $featured->image_url }}" alt="{{ $featured->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                </div>
                <div class="p-8 md:p-12">
                    <p class="eyebrow text-plum-700">{{ __('Featured') }} · {{ $featured->category?->name }}</p>
                    <h2 class="mt-3 font-display text-3xl md:text-4xl leading-tight group-hover:text-plum-700 transition">{{ $featured->title }}</h2>
                    <p class="mt-4 text-ink-500 leading-relaxed line-clamp-3">{{ $featured->excerpt }}</p>
                    <p class="mt-6 text-sm text-ink-300">{{ $featured->published_at?->format('d M Y') }} · {{ $featured->reading_time }} {{ __('min read') }}</p>
                    <span class="mt-6 inline-flex items-center gap-2 font-semibold text-plum-700">{{ __('Read story') }} <x-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1" /></span>
                </div>
            </a>
        </section>
    @endif

    <section class="container {{ $featured ? 'pb-14' : 'py-10 md:py-14' }}">
        <div class="flex flex-wrap items-center gap-2 mb-8">
            <a href="{{ route('blog.index') }}" class="pill {{ !request('category') ? 'bg-plum-950 text-paper ring-plum-950' : 'bg-white text-ink ring-ink/10' }}">{{ __('All') }}</a>
            @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat->slug]) }}" class="pill {{ request('category') === $cat->slug ? 'bg-plum-950 text-paper ring-plum-950' : 'bg-white text-ink ring-ink/10' }}">
                    {{ $cat->name }} <span class="opacity-50">{{ $cat->posts_count }}</span>
                </a>
            @endforeach
            <form method="GET" action="{{ route('blog.index') }}" class="ml-auto flex gap-2 w-full sm:w-auto mt-3 sm:mt-0">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <input type="search" name="q" value="{{ request('q') }}" class="input !h-10 !rounded-full text-sm" placeholder="{{ __('Search stories…') }}">
            </form>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
            @forelse($posts as $post)
                @continue($featured && $post->id === $featured->id && !request()->hasAny(['category','q']))
                <article data-stagger-item>
                    <a href="{{ route('blog.show', $post) }}" class="group block">
                        <div class="aspect-[4/3] overflow-hidden rounded-3xl bg-paper-200">
                            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                        </div>
                        <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-plum-700">{{ $post->category?->name }}</p>
                        <h3 class="mt-1.5 font-display text-xl leading-snug group-hover:text-plum-700 transition">{{ $post->title }}</h3>
                        <p class="mt-2 text-sm text-ink-500 line-clamp-2">{{ $post->excerpt }}</p>
                        <p class="mt-3 text-xs text-ink-300">{{ $post->published_at?->format('d M Y') }} · {{ $post->reading_time }} {{ __('min') }}</p>
                    </a>
                </article>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 rounded-3xl border-2 border-dashed border-ink/10 p-12 text-center">
                    <p class="font-display text-2xl">{{ __('No stories found') }}</p>
                    <a href="{{ route('blog.index') }}" class="btn btn-outline btn-md mt-5">{{ __('Clear filters') }}</a>
                </div>
            @endforelse
        </div>
        <div class="mt-10">{{ $posts->links() }}</div>
    </section>
</x-layouts.app>
