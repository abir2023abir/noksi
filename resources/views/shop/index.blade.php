<x-layouts.app :title="$title" :metaDescription="$subtitle ?? null">
    @php
        $banner = $banner ?? null;
        $color = $color ?? '#5B1B4F';
        $activeFilters = collect(request()->except(['page', 'sort', 'q']))->filter()->count();
    @endphp

    {{-- Page header --}}
    <section class="relative overflow-hidden" style="background: {{ $color }}">
        <div class="absolute inset-0 grain"></div>
        @if($banner)
            <img src="{{ $banner }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30 mix-blend-luminosity" loading="eager">
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent"></div>
        <div class="container relative py-14 md:py-20 text-paper">
            <x-breadcrumb :items="array_filter([__('Shop') => route('shop.index'), $title => null])" class="!text-paper/70 [&_a:hover]:!text-paper [&_span]:!text-paper" />
            <h1 class="mt-4 font-display text-display-lg text-balance" data-split>{{ $title }}</h1>
            @if(!empty($subtitle))
                <p class="mt-4 max-w-xl text-lg text-paper/80">{{ $subtitle }}</p>
            @endif
            @if(isset($category) && $category->children->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach($category->children->where('is_active', true) as $child)
                        <a href="{{ $child->url }}" class="pill ring-white/30 bg-white/10 backdrop-blur text-paper hover:bg-paper hover:text-ink transition">{{ $child->name }}</a>
                    @endforeach
                </div>
            @endif
            @if(isset($flashSale))
                <div class="mt-6 inline-flex items-center gap-3 rounded-2xl bg-paper/15 backdrop-blur px-4 py-2" x-data="countdown('{{ $flashSale->ends_at->toIso8601String() }}')">
                    <x-icon name="zap" class="h-5 w-5" />
                    <span class="text-sm font-semibold">{{ __('Ends in') }}</span>
                    <span class="font-display text-xl tabular-nums" x-text="`${d}:${h}:${m}:${s}`">00:00:00:00</span>
                </div>
            @endif
        </div>
    </section>

    <section class="container py-10 md:py-14" x-data="shopFilters()" x-init="init()">
        <form action="{{ url()->current() }}" method="GET" @change="submit($event)" @submit.prevent="submit($event)" class="grid gap-8 lg:grid-cols-12" x-data="{ mobileFilters: false }">
            @if(!empty($term))<input type="hidden" name="q" value="{{ $term }}">@endif

            {{-- Sidebar filters --}}
            <aside class="lg:col-span-3">
                <div class="lg:hidden flex items-center justify-between mb-4">
                    <button type="button" class="btn btn-outline btn-md" @click="mobileFilters = true"><x-icon name="sliders" class="h-4.5 w-4.5" /> {{ __('Filters') }} @if($activeFilters)<span class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-plum-950 text-[10px] text-marigold-400">{{ $activeFilters }}</span>@endif</button>
                </div>

                <div class="fixed inset-0 z-50 lg:static lg:z-auto" :class="mobileFilters ? 'block' : 'hidden lg:block'" x-cloak>
                    <div class="absolute inset-0 bg-plum-950/60 lg:hidden" @click="mobileFilters = false"></div>
                    <div class="absolute inset-y-0 left-0 w-full max-w-sm overflow-y-auto bg-paper p-6 lg:static lg:max-w-none lg:bg-transparent lg:p-0 lg:overflow-visible">
                        <div class="flex items-center justify-between lg:hidden mb-6">
                            <p class="font-display text-2xl">{{ __('Filters') }}</p>
                            <button type="button" class="icon-btn" @click="mobileFilters = false"><x-icon name="x" class="h-6 w-6" /></button>
                        </div>

                        <div class="sticky top-28 space-y-7">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-lg">{{ __('Refine') }}</p>
                                @if($activeFilters)
                                    <button type="button" class="text-sm font-semibold text-terracotta-700 hover:underline" @click="reset()">{{ __('Clear all') }}</button>
                                @endif
                            </div>

                            {{-- Category --}}
                            @if(!isset($category))
                                <div x-data="{ open: true }">
                                    <button type="button" class="flex w-full items-center justify-between py-2 font-semibold" @click="open = !open">{{ __('Category') }} <x-icon name="chevron-down" class="h-4 w-4 transition" ::class="{ 'rotate-180': open }" /></button>
                                    <div x-show="open" x-collapse class="space-y-1 pt-2">
                                        @foreach($filters['categories'] as $root)
                                            <p class="eyebrow text-ink-300 pt-2 pb-1 text-[10px]">{{ $root->name }}</p>
                                            @foreach($root->children as $child)
                                                <label class="flex items-center gap-3 py-1 text-[15px] cursor-pointer hover:text-plum-700">
                                                    <input type="checkbox" name="categories[]" value="{{ $child->slug }}" class="checkbox" @checked(in_array($child->slug, (array) request('categories', [])))>
                                                    {{ $child->name }}
                                                </label>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Gender --}}
                            <div x-data="{ open: true }">
                                <button type="button" class="flex w-full items-center justify-between py-2 font-semibold" @click="open = !open">{{ __('For') }} <x-icon name="chevron-down" class="h-4 w-4 transition" ::class="{ 'rotate-180': open }" /></button>
                                <div x-show="open" x-collapse class="flex flex-wrap gap-2 pt-2">
                                    @foreach(['women' => __('Women'), 'men' => __('Men'), 'kids' => __('Kids'), 'unisex' => __('Unisex')] as $val => $label)
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="gender[]" value="{{ $val }}" class="peer sr-only" @checked(in_array($val, (array) request('gender', [])))>
                                            <span class="pill ring-ink/15 bg-white peer-checked:bg-plum-950 peer-checked:text-paper peer-checked:ring-plum-950 transition">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Size --}}
                            <div x-data="{ open: true }">
                                <button type="button" class="flex w-full items-center justify-between py-2 font-semibold" @click="open = !open">{{ __('Size') }} <x-icon name="chevron-down" class="h-4 w-4 transition" ::class="{ 'rotate-180': open }" /></button>
                                <div x-show="open" x-collapse class="flex flex-wrap gap-2 pt-2">
                                    @foreach($filters['sizes'] as $size)
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="sizes[]" value="{{ $size->value }}" class="peer sr-only" @checked(in_array($size->value, (array) request('sizes', [])))>
                                            <span class="flex h-10 min-w-10 items-center justify-center rounded-xl border border-ink/15 bg-white px-3 text-sm font-semibold peer-checked:bg-plum-950 peer-checked:text-paper peer-checked:border-plum-950 transition">{{ $size->value }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Colour --}}
                            <div x-data="{ open: true }">
                                <button type="button" class="flex w-full items-center justify-between py-2 font-semibold" @click="open = !open">{{ __('Colour') }} <x-icon name="chevron-down" class="h-4 w-4 transition" ::class="{ 'rotate-180': open }" /></button>
                                <div x-show="open" x-collapse class="flex flex-wrap gap-2.5 pt-2">
                                    @foreach($filters['colors'] as $c)
                                        <label class="cursor-pointer group relative" title="{{ $c->value }}">
                                            <input type="checkbox" name="colors[]" value="{{ $c->value }}" class="peer sr-only" @checked(in_array($c->value, (array) request('colors', [])))>
                                            <span class="block h-9 w-9 rounded-full ring-2 ring-offset-2 ring-offset-paper ring-transparent peer-checked:ring-plum-950 transition group-hover:scale-110 border border-ink/10" style="background: {{ $c->color_code }}"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Fabric --}}
                            @if($filters['fabrics']->isNotEmpty())
                                <div x-data="{ open: false }">
                                    <button type="button" class="flex w-full items-center justify-between py-2 font-semibold" @click="open = !open">{{ __('Fabric') }} <x-icon name="chevron-down" class="h-4 w-4 transition" ::class="{ 'rotate-180': open }" /></button>
                                    <div x-show="open" x-collapse x-cloak class="space-y-1 pt-2">
                                        @foreach($filters['fabrics'] as $f)
                                            <label class="flex items-center gap-3 py-1 text-[15px] cursor-pointer hover:text-plum-700">
                                                <input type="checkbox" name="fabrics[]" value="{{ $f->slug }}" class="checkbox" @checked(in_array($f->slug, (array) request('fabrics', [])))>
                                                {{ $f->value }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Price --}}
                            <div x-data="{ open: true }">
                                <button type="button" class="flex w-full items-center justify-between py-2 font-semibold" @click="open = !open">{{ __('Price') }} <x-icon name="chevron-down" class="h-4 w-4 transition" ::class="{ 'rotate-180': open }" /></button>
                                <div x-show="open" x-collapse class="pt-2">
                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-300 text-sm">{{ setting('currency_symbol', '৳') }}</span><input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" min="0" step="100" class="input !pl-7 !py-2.5 !rounded-xl text-sm" @change.stop @keydown.enter.prevent="submit($event)"></div>
                                        <span class="text-ink-300">–</span>
                                        <div class="relative flex-1"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-300 text-sm">{{ setting('currency_symbol', '৳') }}</span><input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ $filters['priceMax'] }}" min="0" step="100" class="input !pl-7 !py-2.5 !rounded-xl text-sm" @change.stop @keydown.enter.prevent="submit($event)"></div>
                                    </div>
                                    <button type="button" class="btn btn-outline btn-sm mt-3 w-full" @click="submit($event)">{{ __('Apply price') }}</button>
                                </div>
                            </div>

                            {{-- Misc toggles --}}
                            <div class="space-y-2 pt-2 border-t border-ink/5">
                                <label class="flex items-center justify-between py-1.5 cursor-pointer"><span class="text-[15px]">{{ __('In stock only') }}</span><input type="checkbox" name="in_stock" value="1" class="checkbox" @checked(request('in_stock'))></label>
                                <label class="flex items-center justify-between py-1.5 cursor-pointer"><span class="text-[15px]">{{ __('On sale') }}</span><input type="checkbox" name="on_sale" value="1" class="checkbox" @checked(request('on_sale'))></label>
                                <label class="flex items-center justify-between py-1.5 cursor-pointer"><span class="text-[15px] flex items-center gap-1">{{ __('4★ & up') }}</span><input type="checkbox" name="rating" value="4" class="checkbox" @checked(request('rating') == 4)></label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-full lg:hidden" @click="mobileFilters = false">{{ __('Show results') }}</button>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Results --}}
            <div class="lg:col-span-9">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach(['categories', 'sizes', 'colors', 'gender', 'fabrics'] as $key)
                            @foreach((array) request($key, []) as $val)
                                <span class="pill ring-ink/10 bg-white">{{ ucfirst($val) }}</span>
                            @endforeach
                        @endforeach
                    </div>
                    <label class="flex items-center gap-2 text-sm ml-auto">
                        <span class="text-ink-500">{{ __('Sort') }}</span>
                        <select name="sort" class="input !w-auto !py-2.5 !rounded-xl !pr-9 text-sm font-semibold">
                            @foreach(['featured' => __('Featured'), 'newest' => __('Newest'), 'popular' => __('Bestselling'), 'price_asc' => __('Price: low to high'), 'price_desc' => __('Price: high to low'), 'rating' => __('Top rated'), 'name' => __('A–Z')] as $val => $label)
                                <option value="{{ $val }}" @selected(request('sort', 'featured') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div id="shop-results" class="relative min-h-[400px] transition" :class="{ 'opacity-50 pointer-events-none': loading }">
                    @include('shop.partials.product-grid')
                </div>
            </div>
        </form>
    </section>
</x-layouts.app>
