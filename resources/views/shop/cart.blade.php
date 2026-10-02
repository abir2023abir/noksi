<x-layouts.app :title="__('Your bag')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-12 md:py-16">
            <x-breadcrumb :items="[__('Bag') => null]" class="text-paper/60 [&_a]:text-paper/60 [&_a:hover]:text-paper [&_span]:text-paper" />
            <h1 class="mt-4 font-display text-display-sm md:text-display-md" data-split>{{ __('Your bag') }}</h1>
            <p class="mt-2 text-paper/70" x-data x-text="$store.cart.count ? `{{ __('items in your bag') }}`.replace(':n', $store.cart.count) : '{{ __('Empty for now') }}'">
                @if($cart->items->isNotEmpty()){{ trans_choice(':count item|:count items', $cart->count, ['count' => $cart->count]) }}@else{{ __('Empty for now') }}@endif
            </p>
        </div>
    </section>

    <section class="container py-10 md:py-14" x-data="{ couponOpen: {{ $cart->coupon ? 'true' : 'false' }}, code: '{{ $cart->coupon?->code }}' }">
        <template x-if="$store.cart.items.length === 0">
            <div class="mx-auto max-w-lg text-center py-16">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-plum-100 text-plum-700 mb-6 animate-float"><x-icon name="bag" class="h-10 w-10" /></div>
                <h2 class="font-display text-3xl">{{ __('Nothing in here yet') }}</h2>
                <p class="mt-3 text-ink-500">{{ __('Good things take time. Start with something new — or something on sale.') }}</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('shop.new') }}" class="btn btn-primary btn-md">{{ __('Shop new arrivals') }}</a>
                    <a href="{{ route('shop.sale') }}" class="btn btn-outline btn-md">{{ __('Browse sale') }}</a>
                </div>
            </div>
        </template>

        <div class="grid gap-10 lg:grid-cols-12" x-show="$store.cart.items.length > 0" x-cloak>
            <div class="lg:col-span-8 space-y-4" :class="{ 'opacity-60 pointer-events-none': $store.cart.loading }">
                <template x-for="item in $store.cart.items" :key="item.id">
                    <article class="flex gap-4 md:gap-6 rounded-3xl bg-white p-4 md:p-5 ring-1 ring-ink/5">
                        <a :href="item.url" class="h-28 w-24 md:h-36 md:w-28 shrink-0 overflow-hidden rounded-2xl bg-paper-200">
                            <img :src="item.image" :alt="item.name" class="h-full w-full object-cover">
                        </a>
                        <div class="flex flex-1 flex-col min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <a :href="item.url" class="block font-semibold text-lg leading-snug hover:text-plum-700 truncate" x-text="item.name"></a>
                                    <p class="mt-1 text-sm text-ink-300" x-text="item.variant" x-show="item.variant"></p>
                                    <p class="mt-1 text-sm font-semibold" x-text="item.price"></p>
                                </div>
                                <button type="button" class="text-ink-300 hover:text-terracotta-600 transition shrink-0" @click="$store.cart.remove(item.id)" aria-label="{{ __('Remove') }}">
                                    <x-icon name="trash" class="h-5 w-5" />
                                </button>
                            </div>
                            <div class="mt-auto pt-4 flex items-center justify-between gap-3">
                                <div class="inline-flex items-center rounded-full border border-ink/10 bg-paper">
                                    <button type="button" class="flex h-10 w-10 items-center justify-center text-ink-500 hover:text-ink" @click="$store.cart.update(item.id, item.quantity - 1)"><x-icon name="minus" class="h-3.5 w-3.5" /></button>
                                    <span class="w-8 text-center text-sm font-semibold" x-text="item.quantity"></span>
                                    <button type="button" class="flex h-10 w-10 items-center justify-center text-ink-500 hover:text-ink disabled:opacity-30" :disabled="item.quantity >= item.max" @click="$store.cart.update(item.id, item.quantity + 1)"><x-icon name="plus" class="h-3.5 w-3.5" /></button>
                                </div>
                                <span class="font-display text-xl font-semibold" x-text="item.total"></span>
                            </div>
                        </div>
                    </article>
                </template>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-plum-700 hover:underline"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Continue shopping') }}</a>
                    <button type="button" class="text-sm font-semibold text-ink-500 hover:text-terracotta-700" @click="if(confirm('{{ __('Clear your bag?') }}')) { Promise.all($store.cart.items.map(i => $store.cart.remove(i.id))); }">{{ __('Clear bag') }}</button>
                </div>
            </div>

            <aside class="lg:col-span-4">
                <div class="lg:sticky lg:top-28 space-y-5">
                    <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 space-y-5">
                        <h2 class="font-display text-2xl">{{ __('Order summary') }}</h2>

                        <div class="space-y-2.5 text-[15px]">
                            <div class="flex justify-between text-ink-500"><span>{{ __('Subtotal') }}</span><span x-text="$store.cart.subtotal"></span></div>
                            <div class="flex justify-between text-teal-700" x-show="$store.cart.coupon">
                                <span class="inline-flex items-center gap-1.5"><x-icon name="tag" class="h-4 w-4" /> <span x-text="$store.cart.coupon"></span>
                                    <button type="button" class="text-ink-300 hover:text-terracotta-600" @click="$store.cart.removeCoupon()" aria-label="{{ __('Remove coupon') }}"><x-icon name="x" class="h-3.5 w-3.5" /></button>
                                </span>
                                <span x-text="'−' + $store.cart.discount"></span>
                            </div>
                            <div class="flex justify-between text-ink-500"><span>{{ __('Delivery') }}</span><span class="text-ink-300">{{ __('At checkout') }}</span></div>
                            <div class="border-t border-ink/10 pt-3 flex justify-between text-lg font-bold"><span>{{ __('Total') }}</span><span x-text="$store.cart.total"></span></div>
                        </div>

                        <div>
                            <button type="button" class="inline-flex items-center gap-1.5 text-sm font-semibold text-plum-700" @click="couponOpen = !couponOpen" x-show="!$store.cart.coupon">
                                <x-icon name="tag" class="h-4 w-4" /> {{ __('Have a coupon?') }}
                            </button>
                            <form x-show="couponOpen && !$store.cart.coupon" x-cloak class="mt-3 flex gap-2" @submit.prevent="if(await $store.cart.applyCoupon(code)) { couponOpen = false; }">
                                <input type="text" x-model="code" class="input flex-1 uppercase tracking-wider" placeholder="WELCOME10" required>
                                <button class="btn btn-outline btn-md shrink-0">{{ __('Apply') }}</button>
                            </form>
                            <p class="mt-2 text-xs text-ink-300" x-show="!$store.cart.coupon">{{ __('Try WELCOME10 or EID500') }}</p>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg w-full" data-magnetic>{{ __('Checkout') }} <x-icon name="arrow-right" class="h-5 w-5" /></a>

                        <div class="grid grid-cols-3 gap-2 text-center text-[11px] text-ink-500 pt-1">
                            <div><x-icon name="truck" class="mx-auto h-4 w-4 text-teal-700 mb-1" />{{ __('Fast delivery') }}</div>
                            <div><x-icon name="cash" class="mx-auto h-4 w-4 text-plum-700 mb-1" />{{ __('COD available') }}</div>
                            <div><x-icon name="shield" class="mx-auto h-4 w-4 text-marigold-600 mb-1" />{{ __('Secure pay') }}</div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        @if($suggestions->isNotEmpty())
            <div class="mt-16 md:mt-20" x-show="$store.cart.items.length > 0">
                <x-section-heading eyebrow="{{ __('Don\'t miss these') }}" title="{{ __('You might also like') }}" :link="route('shop.index')" />
                <div class="mt-10 grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-4 md:gap-x-6" data-stagger>
                    @foreach($suggestions as $p)
                        <x-product-card :product="$p" data-stagger-item />
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>
