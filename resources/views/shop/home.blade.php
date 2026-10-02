<x-layouts.app>
    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden">
        @if($heroBanners->isNotEmpty())
            <div class="swiper h-[calc(100svh-76px)] min-h-[600px] max-h-[900px]" data-hero-slider>
                <div class="swiper-wrapper">
                    @foreach($heroBanners as $i => $banner)
                        <div class="swiper-slide relative" style="background: {{ $banner->bg_color ?? '#1B0E2B' }}; color: {{ $banner->text_color ?? '#F7F2EA' }}">
                            <div class="absolute inset-0 grain"></div>
                            <div class="container relative h-full grid lg:grid-cols-12 items-center gap-8 py-10">
                                <div class="lg:col-span-6 xl:col-span-5 relative z-10 order-2 lg:order-1">
                                    @if($banner->eyebrow)
                                        <p class="eyebrow mb-5 opacity-80 flex items-center gap-3" data-hero-anim><span class="h-px w-10 bg-current"></span>{{ $banner->eyebrow }}</p>
                                    @endif
                                    <h1 class="font-display text-display-xl font-medium text-balance" @if($i === 0) data-split @else data-hero-anim @endif>{{ $banner->title }}</h1>
                                    @if($banner->subtitle)
                                        <p class="mt-6 max-w-md text-lg md:text-xl opacity-80 leading-relaxed" data-hero-anim>{{ $banner->subtitle }}</p>
                                    @endif
                                    <div class="mt-8 flex flex-wrap items-center gap-3" data-hero-anim>
                                        @if($banner->button_text)
                                            <a href="{{ $banner->button_url ?? route('shop.index') }}" class="btn btn-lg" style="background: {{ $banner->text_color ?? '#F7F2EA' }}; color: {{ $banner->bg_color ?? '#1B0E2B' }}" data-magnetic>{{ $banner->button_text }} <x-icon name="arrow-right" class="h-5 w-5" /></a>
                                        @endif
                                        <a href="{{ route('shop.index') }}" class="btn btn-lg border-2 border-current/40 hover:border-current">{{ __('Shop everything') }}</a>
                                    </div>
                                    <div class="mt-12 hidden md:flex items-center gap-8 opacity-70 text-sm" data-hero-anim>
                                        <span class="flex items-center gap-2"><x-icon name="truck" class="h-5 w-5" /> {{ __('Delivery in 64 districts') }}</span>
                                        <span class="flex items-center gap-2"><x-icon name="refresh" class="h-5 w-5" /> {{ __('7-day exchange') }}</span>
                                        <span class="flex items-center gap-2"><x-icon name="cash" class="h-5 w-5" /> {{ __('Cash on delivery') }}</span>
                                    </div>
                                </div>
                                <div class="lg:col-span-6 xl:col-span-7 relative h-[38vh] lg:h-full order-1 lg:order-2">
                                    <div class="absolute inset-0 lg:inset-y-6 lg:left-8 overflow-hidden rounded-4xl lg:rounded-[3rem]" data-hero-anim>
                                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="h-full w-full object-cover scale-110" data-parallax="0.08" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                                    </div>
                                    {{-- Floating chip --}}
                                    <div class="absolute -bottom-3 left-6 lg:bottom-14 lg:left-0 rounded-3xl bg-paper text-ink p-4 pr-6 shadow-lift flex items-center gap-3 animate-float" data-hero-anim>
                                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl text-white" style="background: {{ $banner->bg_color ?? '#1B0E2B' }}"><x-icon name="sparkles" class="h-5 w-5" /></span>
                                        <div>
                                            <p class="text-xs text-ink-300 font-semibold uppercase tracking-wider">{{ __('This season') }}</p>
                                            <p class="font-semibold">{{ __(':n new pieces', ['n' => bn_number($newArrivals->count() * 4 + 12)]) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="absolute right-6 bottom-6 hidden lg:block writing-vertical eyebrow opacity-50">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($heroBanners->count(), 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination !bottom-6 !left-auto !right-auto container !w-auto flex gap-1.5"></div>
            </div>
        @else
            <div class="container py-24 text-center">
                <h1 class="font-display text-display-xl">NOKSHI</h1>
                <p class="mt-4 text-lg text-ink-500">{{ setting('site_tagline') }}</p>
            </div>
        @endif
    </section>

    {{-- ============ MARQUEE ============ --}}
    <div class="border-y border-ink/5 bg-paper py-4">
        <div class="marquee">
            <div class="marquee__track font-display text-2xl md:text-3xl text-ink">
                @foreach(array_fill(0, 3, [__('Handloom from Rupganj'), __('Cut & sewn in Dhaka'), __('Natural fibres first'), __('Free delivery over :amt', ['amt' => money(setting('free_shipping_threshold', 2000))]), __('7-day easy exchange')]) as $set)
                    @foreach($set as $t)
                        <span class="flex items-center gap-8">{{ $t }} <span class="text-marigold-500">✦</span></span>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============ CATEGORIES ============ --}}
    @if($featuredCategories->isNotEmpty())
        <section class="section">
            <div class="container">
                <x-section-heading eyebrow="{{ __('Shop by') }}" title="{{ __('Find your fit.') }}" subtitle="{{ __('Four worlds, one wardrobe. Pick a lane or wander.') }}" :link="route('shop.index')" />
                <div class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-4 md:grid-rows-2 md:h-[640px]" data-stagger>
                    @foreach($featuredCategories->take(4) as $i => $cat)
                        <a href="{{ $cat->url }}" data-stagger-item
                           class="group relative overflow-hidden rounded-4xl bg-paper-200 {{ $i === 0 ? 'col-span-2 row-span-2 aspect-square md:aspect-auto' : 'aspect-[3/4] md:aspect-auto' }} {{ $i === 1 ? 'md:col-span-2' : '' }}">
                            <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="absolute inset-0 h-full w-full object-cover transition duration-1000 ease-out-expo group-hover:scale-105" loading="lazy">
                            <div class="absolute inset-0 opacity-70 transition duration-500 group-hover:opacity-85" style="background: linear-gradient(to top, {{ $cat->color ?? '#1B0E2B' }} 0%, transparent 60%)"></div>
                            <div class="absolute inset-x-0 bottom-0 p-5 md:p-7 flex items-end justify-between text-paper">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider opacity-80">{{ bn_number($cat->products_count + $cat->children->sum('products_count')) }} {{ __('pieces') }}</p>
                                    <h3 class="font-display {{ $i === 0 ? 'text-4xl md:text-6xl' : 'text-2xl md:text-4xl' }} mt-1">{{ app()->getLocale() === 'bn' && $cat->name_bn ? $cat->name_bn : $cat->name }}</h3>
                                    @if($i === 0 && $cat->children->isNotEmpty())
                                        <div class="mt-3 hidden md:flex flex-wrap gap-2">
                                            @foreach($cat->children->take(4) as $child)
                                                <span class="pill ring-white/30 bg-white/10 backdrop-blur text-paper">{{ $child->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <span class="flex h-11 w-11 md:h-14 md:w-14 shrink-0 items-center justify-center rounded-full bg-paper text-ink transition duration-500 group-hover:bg-marigold-400 group-hover:rotate-45"><x-icon name="arrow-up-right" class="h-5 w-5" /></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ FEATURED ============ --}}
    @if($featured->isNotEmpty())
        <section class="section-tight">
            <div class="container">
                <x-section-heading eyebrow="{{ __('Editor’s picks') }}" title="{{ __('Pieces we keep reaching for.') }}" :link="route('shop.index', ['sort' => 'featured'])">
                    <div class="hidden md:flex gap-2 ml-auto">
                        <button data-prev class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/15 hover:bg-ink hover:text-paper transition disabled:opacity-30"><x-icon name="arrow-left" class="h-5 w-5" /></button>
                        <button data-next class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/15 hover:bg-ink hover:text-paper transition disabled:opacity-30"><x-icon name="arrow-right" class="h-5 w-5" /></button>
                    </div>
                </x-section-heading>
                <div class="mt-10 swiper" data-carousel data-per-view="4">
                    <div class="swiper-wrapper">
                        @foreach($featured as $product)
                            <div class="swiper-slide"><x-product-card :product="$product" /></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ FLASH SALE ============ --}}
    @if($flashSale && $flashSale->items->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="relative overflow-hidden rounded-5xl text-paper p-6 md:p-12" style="background: {{ $flashSale->color ?? '#C2410C' }}" x-data="countdown('{{ $flashSale->ends_at->toIso8601String() }}')">
                    <div class="absolute inset-0 grain"></div>
                    <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-marigold-400/30 blur-3xl"></div>
                    <div class="relative grid gap-8 lg:grid-cols-12 items-center">
                        <div class="lg:col-span-4 reveal">
                            <p class="eyebrow flex items-center gap-2 opacity-90"><x-icon name="zap" class="h-4 w-4" /> {{ __('Flash sale') }}</p>
                            <h2 class="mt-3 font-display text-display-md">{{ $flashSale->title }}</h2>
                            <p class="mt-3 opacity-80 text-lg">{{ $flashSale->subtitle }}</p>
                            <div class="mt-6 flex gap-2" x-show="!ended">
                                @foreach(['d' => __('days'), 'h' => __('hrs'), 'm' => __('min'), 's' => __('sec')] as $k => $label)
                                    <div class="flex flex-col items-center rounded-2xl bg-paper/15 backdrop-blur px-3 py-2 min-w-[64px]">
                                        <span class="font-display text-3xl font-semibold tabular-nums" x-text="{{ $k }}">00</span>
                                        <span class="text-[10px] uppercase tracking-wider opacity-80">{{ $label }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('shop.flash') }}" class="btn btn-lg mt-8 bg-paper text-ink hover:bg-marigold-400">{{ __('Shop the drop') }} <x-icon name="arrow-right" class="h-5 w-5" /></a>
                        </div>
                        <div class="lg:col-span-8 grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4" data-stagger>
                            @foreach($flashSale->items->take(6) as $item)
                                @php $p = $item->product; @endphp
                                <a href="{{ $p->url }}" class="group rounded-3xl bg-paper text-ink p-2.5 flex gap-3 items-center" data-stagger-item>
                                    <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="h-20 w-16 shrink-0 rounded-2xl object-cover" loading="lazy">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold group-hover:text-terracotta-700">{{ $p->name }}</p>
                                        <div class="mt-1 flex items-baseline gap-2"><span class="font-bold text-terracotta-700">{{ money($item->sale_price) }}</span><span class="text-xs text-ink-300 line-through">{{ money($p->price) }}</span></div>
                                        @if($item->quantity_limit)
                                            <div class="mt-2 h-1 w-full rounded-full bg-ink/10"><div class="h-full rounded-full bg-terracotta-600" style="width: {{ $item->sold_percent }}%"></div></div>
                                            <p class="mt-1 text-[10px] text-ink-300">{{ __(':n left', ['n' => max(0, $item->quantity_limit - $item->sold)]) }}</p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ COLLECTIONS ============ --}}
    @if($collections->isNotEmpty())
        <section class="section-tight">
            <div class="container">
                <x-section-heading eyebrow="{{ __('Collections') }}" title="{{ __('Dressed for the season.') }}" />
                <div class="mt-10 grid gap-5 md:grid-cols-3" data-stagger>
                    @foreach($collections as $i => $col)
                        <a href="{{ route('shop.collection', $col->slug) }}" data-stagger-item class="group relative aspect-[4/5] overflow-hidden rounded-4xl {{ $i === 1 ? 'md:-translate-y-8' : '' }}">
                            <img src="{{ $col->image_url }}" alt="{{ $col->name }}" class="absolute inset-0 h-full w-full object-cover transition duration-1000 ease-out-expo group-hover:scale-105" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/10 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-6 text-paper">
                                <span class="badge text-white mb-3" style="background: {{ $col->color ?? '#5B1B4F' }}">{{ __('Collection') }}</span>
                                <h3 class="font-display text-3xl">{{ $col->name }}</h3>
                                <p class="mt-1 text-paper/80 text-sm">{{ $col->subtitle }}</p>
                                <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold"><span class="link-underline">{{ __('Explore') }}</span> <x-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1" /></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ NEW ARRIVALS ============ --}}
    @if($newArrivals->isNotEmpty())
        <section class="section">
            <div class="container">
                <x-section-heading eyebrow="{{ __('Just in') }}" title="{{ __('New this week.') }}" subtitle="{{ __('Fresh off the loom and the cutting table.') }}" :link="route('shop.new')" />
                <div class="mt-10 grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-4 md:gap-x-6" data-stagger>
                    @foreach($newArrivals->take(8) as $product)
                        <x-product-card :product="$product" data-stagger-item />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ PROMO / STORY ============ --}}
    @if($promoBanner)
        <section class="section-tight">
            <div class="container">
                <div class="relative overflow-hidden rounded-5xl grid lg:grid-cols-2 min-h-[520px]" style="background: {{ $promoBanner->bg_color ?? '#1B0E2B' }}; color: {{ $promoBanner->text_color ?? '#F5C24B' }}">
                    <div class="relative overflow-hidden min-h-[320px]">
                        <img src="{{ $promoBanner->image_url }}" alt="{{ $promoBanner->title }}" class="absolute inset-0 h-full w-full object-cover scale-110" data-parallax="0.1" loading="lazy">
                    </div>
                    <div class="relative p-8 md:p-14 flex flex-col justify-center grain">
                        @if($promoBanner->eyebrow)<p class="eyebrow opacity-80 reveal">{{ $promoBanner->eyebrow }}</p>@endif
                        <h2 class="mt-4 font-display text-display-md reveal">{{ $promoBanner->title }}</h2>
                        <p class="mt-5 text-lg opacity-80 max-w-md reveal">{{ $promoBanner->subtitle }}</p>
                        <div class="mt-8 grid grid-cols-3 gap-4 max-w-md reveal">
                            @foreach([['40+', __('Makers')], ['64', __('Districts')], ['100%', __('Natural fibre')]] as [$num, $lab])
                                <div><p class="font-display text-3xl md:text-4xl">{{ bn_number($num) }}</p><p class="text-xs uppercase tracking-wider opacity-70 mt-1">{{ $lab }}</p></div>
                            @endforeach
                        </div>
                        @if($promoBanner->button_text)
                            <a href="{{ $promoBanner->button_url }}" class="btn btn-lg mt-10 self-start reveal" style="background: {{ $promoBanner->text_color ?? '#F5C24B' }}; color: {{ $promoBanner->bg_color ?? '#1B0E2B' }}">{{ $promoBanner->button_text }} <x-icon name="arrow-right" class="h-5 w-5" /></a>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ TRENDING ============ --}}
    @if($trending->isNotEmpty())
        <section class="section-tight">
            <div class="container">
                <x-section-heading eyebrow="{{ __('Trending') }}" title="{{ __('What Dhaka is wearing.') }}" :link="route('shop.index', ['sort' => 'popular'])" linkText="{{ __('All bestsellers') }}">
                    <div class="hidden md:flex gap-2 ml-auto">
                        <button data-prev class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/15 hover:bg-ink hover:text-paper transition disabled:opacity-30"><x-icon name="arrow-left" class="h-5 w-5" /></button>
                        <button data-next class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/15 hover:bg-ink hover:text-paper transition disabled:opacity-30"><x-icon name="arrow-right" class="h-5 w-5" /></button>
                    </div>
                </x-section-heading>
                <div class="mt-10 swiper" data-carousel data-per-view="4">
                    <div class="swiper-wrapper">
                        @foreach($trending as $product)
                            <div class="swiper-slide"><x-product-card :product="$product" /></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ VALUES ============ --}}
    <section class="section-tight">
        <div class="container grid gap-4 md:grid-cols-4" data-stagger>
            @foreach([
                ['truck', __('Nationwide delivery'), __('1–2 days in Dhaka, 3–5 days everywhere else.'), 'bg-teal-50 text-teal-700'],
                ['refresh', __('7-day exchange'), __('Wrong size? Swap it, free inside Dhaka.'), 'bg-marigold-50 text-marigold-700'],
                ['cash', __('Pay how you like'), __('Cash on delivery, bKash, Nagad or card.'), 'bg-plum-50 text-plum-700'],
                ['leaf', __('Made with intention'), __('Natural fibres, small batches, named makers.'), 'bg-terracotta-50 text-terracotta-700'],
            ] as [$icon, $t, $d, $c])
                <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 shadow-card" data-stagger-item>
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $c }}"><x-icon :name="$icon" class="h-6 w-6" /></span>
                    <h3 class="mt-4 font-semibold text-lg">{{ $t }}</h3>
                    <p class="mt-1 text-sm text-ink-500 leading-relaxed">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ TESTIMONIALS ============ --}}
    @if($testimonials->isNotEmpty())
        <section class="section bg-plum-950 text-paper relative overflow-hidden">
            <div class="absolute inset-0 grain"></div>
            <div class="absolute left-1/2 top-0 h-[600px] w-[600px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-plum-700/40 blur-3xl"></div>
            <div class="container relative">
                <x-section-heading eyebrow="{{ __('Word of mouth') }}" title="{{ __('People who wear Nokshi.') }}" :dark="true" align="center" />
                <div class="mt-12 swiper" data-testimonials>
                    <div class="swiper-wrapper pb-12">
                        @foreach($testimonials as $t)
                            <div class="swiper-slide h-auto">
                                <figure class="h-full flex flex-col rounded-4xl bg-white/5 ring-1 ring-white/10 backdrop-blur p-7">
                                    <x-rating :value="$t->rating" />
                                    <blockquote class="mt-4 flex-1 text-lg leading-relaxed text-paper/90">“{{ $t->body }}”</blockquote>
                                    <figcaption class="mt-6 flex items-center gap-3">
                                        <img src="{{ $t->avatar_url }}" alt="{{ $t->name }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-marigold-400/50">
                                        <div><p class="font-semibold">{{ $t->name }}</p><p class="text-sm text-paper/60">{{ $t->role }}</p></div>
                                    </figcaption>
                                </figure>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination !bottom-0"></div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ JOURNAL ============ --}}
    @if($posts->isNotEmpty())
        <section class="section">
            <div class="container">
                <x-section-heading eyebrow="{{ __('Journal') }}" title="{{ __('Notes from the studio.') }}" :link="route('blog.index')" linkText="{{ __('Read the journal') }}" />
                <div class="mt-10 grid gap-8 md:grid-cols-3" data-stagger>
                    @foreach($posts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="group" data-stagger-item>
                            <div class="aspect-[4/3] overflow-hidden rounded-3xl bg-paper-200">
                                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-1000 ease-out-expo group-hover:scale-105" loading="lazy">
                            </div>
                            <div class="mt-5 flex items-center gap-3 text-xs font-semibold uppercase tracking-wider text-ink-300">
                                @if($post->category)<span class="text-plum-700">{{ $post->category->name }}</span><span>·</span>@endif
                                <span>{{ $post->reading_time }} {{ __('min read') }}</span>
                            </div>
                            <h3 class="mt-2 font-display text-2xl leading-snug group-hover:text-plum-700 transition">{{ $post->title }}</h3>
                            <p class="mt-2 text-ink-500 line-clamp-2">{{ $post->excerpt }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ INSTAGRAM ============ --}}
    @if(setting('show_instagram', '1') === '1')
        <section class="pb-8">
            <div class="container text-center reveal mb-8">
                <p class="eyebrow text-plum-700">{{ __('On Instagram') }}</p>
                <a href="{{ setting('social_instagram', '#') }}" target="_blank" rel="noopener" class="font-display text-display-sm link-underline">@nokshi</a>
            </div>
            <div class="marquee">
                <div class="marquee__track !gap-3 animate-marquee-reverse">
                    @foreach(array_merge($trending->pluck('image_url')->all(), $featured->pluck('image_url')->all()) as $img)
                        <a href="{{ setting('social_instagram', '#') }}" target="_blank" rel="noopener" class="group relative h-56 w-56 md:h-72 md:w-72 shrink-0 overflow-hidden rounded-3xl">
                            <img src="{{ $img }}" alt="" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy">
                            <span class="absolute inset-0 flex items-center justify-center bg-plum-950/40 opacity-0 transition group-hover:opacity-100 text-paper"><x-icon name="instagram" class="h-8 w-8" /></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.app>
