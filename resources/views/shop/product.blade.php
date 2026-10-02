<x-layouts.app :title="$product->meta_title ?: $product->name" :metaDescription="$product->meta_description ?: $product->short_description" :ogImage="$product->image_url">
    @php
        $variantsJson = $product->variants->map(fn ($v) => [
            'id' => $v->id, 'size' => $v->size, 'color' => $v->color, 'color_code' => $v->color_code,
            'stock' => (int) $v->stock, 'image' => $v->image_url,
            'price_formatted' => money($flashSale ? $flashSale->sale_price : $v->effective_price),
        ]);
        $imageIndex = $product->images->values()->mapWithKeys(fn ($img, $i) => [$img->url => $i]);
        $price = $product->current_price;
        $original = $product->original_price;
    @endphp

    <div class="container pt-6 md:pt-8">
        <x-breadcrumb :items="array_filter([
            __('Shop') => route('shop.index'),
            $product->category?->parent?->name => $product->category?->parent?->url,
            $product->category?->name => $product->category?->url,
            $product->name => null,
        ], fn ($v, $k) => $k !== '', ARRAY_FILTER_USE_BOTH)" />
    </div>

    <section class="container py-8 md:py-12 grid gap-10 lg:grid-cols-12"
             x-data="productOptions({ productId: {{ $product->id }}, variants: @js($variantsJson), price: @js(money($price)), defaultColor: @js($product->variants->first()?->color), imageIndexByUrl: @js($imageIndex) })">

        {{-- Gallery --}}
        <div class="lg:col-span-7">
            <div class="grid gap-3 md:grid-cols-[88px_1fr]">
                <div class="order-2 md:order-1 flex md:flex-col gap-2.5 overflow-x-auto no-scrollbar md:max-h-[720px]">
                    @foreach($product->images as $i => $img)
                        <button type="button" @click="galleryIndex = {{ $i }}" class="relative h-20 w-16 md:h-[104px] md:w-full shrink-0 overflow-hidden rounded-2xl ring-2 ring-offset-2 ring-offset-paper transition" :class="galleryIndex === {{ $i }} ? 'ring-plum-950' : 'ring-transparent hover:ring-ink/20'">
                            <img src="{{ $img->url }}" alt="{{ $img->alt ?? $product->name }}" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                    @if($product->video_url)
                        <a href="{{ $product->video_url }}" target="_blank" class="flex h-20 w-16 md:h-[104px] md:w-full shrink-0 items-center justify-center rounded-2xl bg-ink text-paper"><x-icon name="eye" class="h-5 w-5" /></a>
                    @endif
                </div>
                <div class="order-1 md:order-2 relative aspect-[3/4] overflow-hidden rounded-4xl bg-paper-200 group" x-data="{ zoom: false, x: 50, y: 50 }"
                     @mousemove="const r = $el.getBoundingClientRect(); x = (($event.clientX - r.left) / r.width) * 100; y = (($event.clientY - r.top) / r.height) * 100" @mouseenter="zoom = true" @mouseleave="zoom = false">
                    @foreach($product->images as $i => $img)
                        <img src="{{ $img->url }}" alt="{{ $img->alt ?? $product->name }}" x-show="galleryIndex === {{ $i }}" x-transition.opacity.duration.400ms
                             class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 cursor-zoom-in" :style="zoom ? `transform: scale(1.8); transform-origin: ${x}% ${y}%` : ''" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                    @endforeach
                    <div class="absolute left-4 top-4 flex flex-col gap-1.5 pointer-events-none">
                        @if($product->discount_percent > 0)<span class="badge bg-terracotta-600 text-white">−{{ $product->discount_percent }}%</span>@endif
                        @if($product->badge)<span class="badge text-white" style="background: {{ $product->badge_color ?? '#5B1B4F' }}">{{ $product->badge }}</span>@elseif($product->is_new)<span class="badge bg-marigold-400 text-plum-950">{{ __('New') }}</span>@endif
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 flex justify-between pointer-events-none">
                        <span class="pill ring-white/0 bg-white/80 backdrop-blur text-ink text-xs" x-text="`${galleryIndex + 1} / {{ $product->images->count() }}`"></span>
                    </div>
                    <button type="button" class="absolute left-3 top-1/2 -translate-y-1/2 flex h-11 w-11 items-center justify-center rounded-full bg-white/90 shadow-soft opacity-0 transition group-hover:opacity-100" @click="galleryIndex = (galleryIndex - 1 + {{ $product->images->count() }}) % {{ $product->images->count() }}"><x-icon name="chevron-left" class="h-5 w-5" /></button>
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 flex h-11 w-11 items-center justify-center rounded-full bg-white/90 shadow-soft opacity-0 transition group-hover:opacity-100" @click="galleryIndex = (galleryIndex + 1) % {{ $product->images->count() }}"><x-icon name="chevron-right" class="h-5 w-5" /></button>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="lg:col-span-5">
            <div class="lg:sticky lg:top-28 space-y-7">
                <div>
                    <div class="flex items-center gap-3 text-sm">
                        @if($product->brand)<a href="{{ route('shop.brand', $product->brand->slug) }}" class="font-semibold text-plum-700 uppercase tracking-wider text-xs">{{ $product->brand->name }}</a>@endif
                        @if($product->rating_count)
                            <a href="#reviews" class="flex items-center gap-1.5 text-ink-500 hover:text-ink"><x-rating :value="$product->rating_avg" size="h-3.5 w-3.5" /> <span>{{ number_format($product->rating_avg, 1) }} · {{ $product->rating_count }} {{ __('reviews') }}</span></a>
                        @endif
                    </div>
                    <h1 class="mt-2 font-display text-display-sm md:text-4xl leading-tight text-balance">{{ app()->getLocale() === 'bn' && $product->name_bn ? $product->name_bn : $product->name }}</h1>
                    @if($product->name_bn && app()->getLocale() !== 'bn')<p class="mt-1 font-bangla text-ink-300">{{ $product->name_bn }}</p>@endif
                    <div class="mt-4 flex items-baseline gap-3">
                        <span class="font-display text-3xl font-semibold {{ $original ? 'text-terracotta-700' : '' }}" x-text="price">{{ money($price) }}</span>
                        @if($original)<span class="text-lg text-ink-300 line-through">{{ money($original) }}</span><span class="badge bg-terracotta-50 text-terracotta-700">{{ __('Save :p%', ['p' => $product->discount_percent]) }}</span>@endif
                    </div>
                    @if($flashSale)
                        <div class="mt-3 inline-flex items-center gap-2 rounded-xl bg-terracotta-50 px-3 py-2 text-sm text-terracotta-800" x-data="countdown('{{ $flashSale->flashSale->ends_at->toIso8601String() }}')">
                            <x-icon name="zap" class="h-4 w-4" /> {{ __('Flash price ends in') }} <span class="font-bold tabular-nums" x-text="`${h}:${m}:${s}`"></span>
                        </div>
                    @endif
                </div>

                @if($product->short_description)
                    <p class="text-ink-500 leading-relaxed text-pretty">{{ $product->short_description }}</p>
                @endif

                {{-- Colour --}}
                <template x-if="colors.length">
                    <div>
                        <div class="flex items-center justify-between mb-2.5"><span class="font-semibold text-sm">{{ __('Colour') }}: <span class="text-ink-500 font-normal" x-text="color"></span></span></div>
                        <div class="flex flex-wrap gap-2.5">
                            <template x-for="c in colors" :key="c.name">
                                <button type="button" @click="selectColor(c.name)" :title="c.name"
                                        class="h-10 w-10 rounded-full border border-ink/10 ring-2 ring-offset-2 ring-offset-paper transition hover:scale-110" :class="color === c.name ? 'ring-plum-950' : 'ring-transparent'" :style="`background:${c.code || '#ccc'}`"></button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Size --}}
                <template x-if="sizes.length">
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="font-semibold text-sm">{{ __('Size') }}: <span class="text-ink-500 font-normal" x-text="size || '{{ __('Select') }}'"></span></span>
                            <a href="{{ route('page.show', 'size-guide') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-plum-700 hover:underline"><x-icon name="ruler" class="h-3.5 w-3.5" /> {{ __('Size guide') }}</a>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="s in sizes" :key="s">
                                <button type="button" @click="size = s" :disabled="stockFor(s) === 0"
                                        class="relative flex h-12 min-w-[52px] items-center justify-center rounded-2xl border px-4 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-40 disabled:line-through"
                                        :class="size === s ? 'bg-plum-950 text-paper border-plum-950' : 'bg-white border-ink/15 hover:border-ink'">
                                    <span x-text="s"></span>
                                    <span x-show="stockFor(s) > 0 && stockFor(s) <= 3" class="absolute -top-2 -right-2 badge bg-terracotta-600 text-white !px-1.5 !py-0.5 !text-[9px]" x-text="stockFor(s) + ' {{ __('left') }}'"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Qty + Add --}}
                <div class="flex gap-3">
                    <div class="inline-flex items-center rounded-full border border-ink/15 bg-white h-14">
                        <button type="button" class="flex h-full w-12 items-center justify-center text-ink-500 hover:text-ink" @click="qty = Math.max(1, qty - 1)"><x-icon name="minus" class="h-4 w-4" /></button>
                        <span class="w-8 text-center font-semibold" x-text="qty"></span>
                        <button type="button" class="flex h-full w-12 items-center justify-center text-ink-500 hover:text-ink" @click="qty = Math.min(maxQty, qty + 1)"><x-icon name="plus" class="h-4 w-4" /></button>
                    </div>
                    @if($product->is_in_stock)
                        <button type="button" class="btn btn-primary btn-lg flex-1" :disabled="$store.cart.loading || (variants.length && selected && selected.stock === 0)" @click="add()" data-magnetic>
                            <x-icon name="bag" class="h-5 w-5" />
                            <span x-text="(variants.length && !selected) ? '{{ __('Select options') }}' : '{{ __('Add to bag') }}'"></span>
                        </button>
                    @else
                        <button type="button" class="btn btn-outline btn-lg flex-1" disabled>{{ __('Sold out') }}</button>
                    @endif
                    <button type="button" @click="$store.wishlist.toggle({{ $product->id }})" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full border border-ink/15 bg-white transition hover:border-terracotta-600 hover:text-terracotta-600" :class="{ 'text-terracotta-600 border-terracotta-600': $store.wishlist.has({{ $product->id }}) }" aria-label="{{ __('Wishlist') }}">
                        <template x-if="$store.wishlist.has({{ $product->id }})"><x-icon name="heart-fill" class="h-6 w-6" /></template>
                        <template x-if="!$store.wishlist.has({{ $product->id }})"><x-icon name="heart" class="h-6 w-6" /></template>
                    </button>
                </div>

                <template x-if="selected && selected.stock > 0 && selected.stock <= 5">
                    <p class="text-sm font-semibold text-terracotta-700 flex items-center gap-1.5"><x-icon name="flame" class="h-4 w-4" /> <span x-text="`{{ __('Only') }} ${selected.stock} {{ __('left in this size') }}`"></span></p>
                </template>

                {{-- Trust --}}
                <div class="grid grid-cols-3 gap-3 text-center text-xs text-ink-500">
                    <div class="rounded-2xl bg-white p-3 ring-1 ring-ink/5"><x-icon name="truck" class="mx-auto h-5 w-5 text-teal-700 mb-1.5" />{{ __('Delivery in 1–5 days') }}</div>
                    <div class="rounded-2xl bg-white p-3 ring-1 ring-ink/5"><x-icon name="refresh" class="mx-auto h-5 w-5 text-marigold-600 mb-1.5" />{{ __('7-day exchange') }}</div>
                    <div class="rounded-2xl bg-white p-3 ring-1 ring-ink/5"><x-icon name="cash" class="mx-auto h-5 w-5 text-plum-700 mb-1.5" />{{ __('Cash on delivery') }}</div>
                </div>

                {{-- Accordions --}}
                <div class="divide-y divide-ink/10 border-y border-ink/10" x-data="{ open: 'details' }">
                    <div>
                        <button type="button" class="flex w-full items-center justify-between py-4 font-semibold" @click="open = open === 'details' ? null : 'details'">{{ __('Details') }} <x-icon name="plus" class="h-4 w-4 transition" ::class="{ 'rotate-45': open === 'details' }" /></button>
                        <div x-show="open === 'details'" x-collapse><div class="prose-nokshi prose-p:text-ink-500 prose-li:text-ink-500 text-[15px] pb-5">{!! $product->description !!}</div></div>
                    </div>
                    @if($product->material || $product->care_instructions)
                        <div>
                            <button type="button" class="flex w-full items-center justify-between py-4 font-semibold" @click="open = open === 'care' ? null : 'care'">{{ __('Fabric & care') }} <x-icon name="plus" class="h-4 w-4 transition" ::class="{ 'rotate-45': open === 'care' }" /></button>
                            <div x-show="open === 'care'" x-collapse x-cloak><div class="pb-5 text-[15px] text-ink-500 space-y-2">
                                @if($product->material)<p><strong class="text-ink">{{ __('Fabric') }}:</strong> {{ $product->material }}</p>@endif
                                @if($product->care_instructions)<p>{{ $product->care_instructions }}</p>@endif
                            </div></div>
                        </div>
                    @endif
                    <div>
                        <button type="button" class="flex w-full items-center justify-between py-4 font-semibold" @click="open = open === 'ship' ? null : 'ship'">{{ __('Shipping & returns') }} <x-icon name="plus" class="h-4 w-4 transition" ::class="{ 'rotate-45': open === 'ship' }" /></button>
                        <div x-show="open === 'ship'" x-collapse x-cloak><div class="pb-5 text-[15px] text-ink-500 space-y-2">
                            <p>{{ __('Inside Dhaka: 1–2 days (free over :amt). Outside Dhaka: 3–5 days via courier.', ['amt' => money(setting('free_shipping_threshold', 2000))]) }}</p>
                            <p>{{ __('7-day exchange on unworn items with tags. Exchanges are free inside Dhaka.') }}</p>
                        </div></div>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-sm text-ink-500">
                    <span>{{ __('SKU') }}: <span class="font-mono text-ink">{{ $product->sku }}</span></span>
                    <span class="text-ink-300">·</span>
                    <span class="flex items-center gap-2">{{ __('Share') }}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="hover:text-ink"><x-icon name="facebook" class="h-4 w-4" /></a>
                        <a href="https://wa.me/?text={{ urlencode($product->name.' '.url()->current()) }}" target="_blank" class="hover:text-ink"><x-icon name="whatsapp" class="h-4 w-4" /></a>
                        <button type="button" class="hover:text-ink" @click="navigator.clipboard.writeText(location.href); $store.ui.toast('{{ __('Link copied') }}')"><x-icon name="copy" class="h-4 w-4" /></button>
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Reviews --}}
    <section id="reviews" class="container section-tight border-t border-ink/5">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="eyebrow text-plum-700">{{ __('Reviews') }}</p>
                <h2 class="mt-2 font-display text-display-sm">{{ __('What people say') }}</h2>
                @if($product->rating_count)
                    <div class="mt-6 flex items-end gap-4">
                        <span class="font-display text-7xl leading-none">{{ number_format($product->rating_avg, 1) }}</span>
                        <div class="pb-2"><x-rating :value="$product->rating_avg" size="h-5 w-5" /><p class="text-sm text-ink-500 mt-1">{{ __('Based on :n reviews', ['n' => $product->rating_count]) }}</p></div>
                    </div>
                    <div class="mt-5 space-y-1.5">
                        @foreach($ratingBreakdown as $stars => $count)
                            <div class="flex items-center gap-3 text-sm"><span class="w-3 text-ink-500">{{ $stars }}</span><x-icon name="star-fill" class="h-3.5 w-3.5 text-marigold-500" /><div class="h-2 flex-1 rounded-full bg-ink/10 overflow-hidden"><div class="h-full rounded-full bg-marigold-400" style="width: {{ $product->rating_count ? $count / $product->rating_count * 100 : 0 }}%"></div></div><span class="w-6 text-right text-ink-500">{{ $count }}</span></div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 text-ink-500">{{ __('No reviews yet. Be the first to share your thoughts.') }}</p>
                @endif

                <div class="mt-8" x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }">
                    @if($canReview || !auth()->check())
                        <button type="button" class="btn btn-outline btn-md" @click="open = !open">{{ __('Write a review') }}</button>
                        <form x-show="open" x-collapse x-cloak method="POST" action="{{ route('product.review', $product->slug) }}" enctype="multipart/form-data" class="mt-5 space-y-4 rounded-3xl bg-white p-5 ring-1 ring-ink/5">
                            @csrf
                            <div x-data="{ r: {{ old('rating', 5) }} }">
                                <label class="label">{{ __('Your rating') }}</label>
                                <input type="hidden" name="rating" :value="r">
                                <div class="flex gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" @click="r = {{ $i }}" class="text-marigold-500 transition hover:scale-110"><template x-if="r >= {{ $i }}"><x-icon name="star-fill" class="h-7 w-7" /></template><template x-if="r < {{ $i }}"><x-icon name="star" class="h-7 w-7 text-ink-300" /></template></button>
                                    @endfor
                                </div>
                            </div>
                            @guest
                                <div><label class="label">{{ __('Your name') }}</label><input type="text" name="name" value="{{ old('name') }}" class="input" required></div>
                            @endguest
                            <div><label class="label">{{ __('Title') }}</label><input type="text" name="title" value="{{ old('title') }}" class="input" placeholder="{{ __('Sum it up') }}"></div>
                            <div><label class="label">{{ __('Review') }}</label><textarea name="body" rows="4" class="input" required placeholder="{{ __('Fit, fabric, colour — what should others know?') }}">{{ old('body') }}</textarea>@error('body')<p class="mt-1 text-xs text-terracotta-700">{{ $message }}</p>@enderror</div>
                            <div><label class="label">{{ __('Photos (optional)') }}</label><input type="file" name="images[]" multiple accept="image/*" class="block w-full text-sm text-ink-500 file:mr-3 file:rounded-full file:border-0 file:bg-plum-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-plum-700"></div>
                            <button class="btn btn-primary btn-md">{{ __('Submit review') }}</button>
                        </form>
                    @else
                        <p class="text-sm text-ink-500">{{ __('You have already reviewed this product. Thank you!') }}</p>
                    @endif
                </div>
            </div>
            <div class="lg:col-span-8 space-y-5">
                @forelse($reviews as $review)
                    <article class="rounded-3xl bg-white p-6 ring-1 ring-ink/5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-plum-100 font-bold text-plum-700">{{ mb_substr($review->author_name, 0, 1) }}</span>
                                <div><p class="font-semibold">{{ $review->author_name }} @if($review->is_verified)<span class="ml-1 inline-flex items-center gap-1 text-xs text-teal-700 font-semibold"><x-icon name="check-circle" class="h-3.5 w-3.5" /> {{ __('Verified buyer') }}</span>@endif</p><p class="text-xs text-ink-300">{{ $review->created_at->diffForHumans() }}</p></div>
                            </div>
                            <x-rating :value="$review->rating" />
                        </div>
                        @if($review->title)<h3 class="mt-4 font-semibold text-lg">{{ $review->title }}</h3>@endif
                        <p class="mt-2 text-ink-500 leading-relaxed">{{ $review->body }}</p>
                        @if($review->images)
                            <div class="mt-4 flex gap-2">@foreach($review->images as $img)<img src="{{ img($img) }}" class="h-20 w-20 rounded-xl object-cover" alt="">@endforeach</div>
                        @endif
                        @if($review->admin_reply)
                            <div class="mt-4 rounded-2xl bg-paper p-4 text-sm"><p class="font-semibold text-plum-700 mb-1">{{ __('Reply from NOKSHI') }}</p><p class="text-ink-500">{{ $review->admin_reply }}</p></div>
                        @endif
                    </article>
                @empty
                    <div class="rounded-3xl border-2 border-dashed border-ink/10 p-10 text-center text-ink-500">{{ __('Reviews will appear here once approved.') }}</div>
                @endforelse
                <div>{{ $reviews->links() }}</div>
            </div>
        </div>
    </section>

    {{-- Related --}}
    @if($related->isNotEmpty())
        <section class="container section-tight">
            <x-section-heading eyebrow="{{ __('Complete the look') }}" title="{{ __('You might also like') }}" :link="$product->category?->url" />
            <div class="mt-10 grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-4 md:gap-x-6" data-stagger>
                @foreach($related as $p)<x-product-card :product="$p" data-stagger-item />@endforeach
            </div>
        </section>
    @endif

    @if($recentlyViewed->isNotEmpty())
        <section class="container section-tight">
            <x-section-heading eyebrow="{{ __('Recently viewed') }}" title="{{ __('Pick up where you left off') }}" />
            <div class="mt-10 grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-4 md:gap-x-6">
                @foreach($recentlyViewed->take(4) as $p)<x-product-card :product="$p" />@endforeach
            </div>
        </section>
    @endif

    {{-- Sticky mobile add bar --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-ink/10 bg-paper/95 backdrop-blur p-3 lg:hidden" x-data x-show="true">
        <div class="flex items-center gap-3">
            <div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold">{{ $product->name }}</p><p class="text-sm font-bold">{{ money($price) }}</p></div>
            <button type="button" class="btn btn-primary btn-md" @click="document.querySelector('[x-data^=productOptions]')?.scrollIntoView({behavior:'smooth'})">{{ __('Choose options') }}</button>
        </div>
    </div>

    @push('head')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org', '@type' => 'Product', 'name' => $product->name, 'image' => $product->images->pluck('url'),
            'description' => $product->short_description, 'sku' => $product->sku, 'brand' => ['@type' => 'Brand', 'name' => $product->brand?->name ?? 'NOKSHI'],
            'offers' => ['@type' => 'Offer', 'priceCurrency' => 'BDT', 'price' => $price, 'availability' => $product->is_in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock', 'url' => url()->current()],
        ] + ($product->rating_count ? ['aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => $product->rating_avg, 'reviewCount' => $product->rating_count]] : []), JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush
</x-layouts.app>
