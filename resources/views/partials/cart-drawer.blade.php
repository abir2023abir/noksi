<div x-show="$store.ui.cartOpen" x-cloak class="fixed inset-0 z-[60]" role="dialog" aria-modal="true" aria-label="{{ __('Shopping bag') }}">
    <div x-show="$store.ui.cartOpen" x-transition.opacity.duration.300ms class="absolute inset-0 bg-plum-950/60 backdrop-blur-sm" @click="$store.ui.cartOpen = false"></div>

    <aside x-show="$store.ui.cartOpen"
           x-transition:enter="transition ease-out-expo duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out-quart duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
           class="absolute right-0 top-0 h-full w-full max-w-md flex flex-col bg-paper shadow-lift"
           x-trap.noscroll="$store.ui.cartOpen" @keydown.escape.window="$store.ui.cartOpen = false">

        <div class="flex items-center justify-between border-b border-ink/5 px-6 py-5">
            <h2 class="font-display text-2xl">{{ __('Your bag') }} <span class="text-ink-300 text-lg" x-text="`(${$store.cart.count})`"></span></h2>
            <button class="icon-btn -mr-2" @click="$store.ui.cartOpen = false" aria-label="{{ __('Close') }}"><x-icon name="x" class="h-6 w-6" /></button>
        </div>

        {{-- Free shipping progress --}}
        @php $threshold = (float) setting('free_shipping_threshold', 2000); @endphp
        <div class="px-6 pt-5" x-show="$store.cart.count > 0" x-data="{ threshold: {{ $threshold }} }">
            <template x-if="$store.cart.count > 0">
                <div>
                    <p class="text-sm text-ink-500" x-html="(parseFloat(String($store.cart.subtotal).replace(/[^0-9.]/g,'')) >= threshold)
                        ? '<span class=\'font-semibold text-teal-700\'>{{ __('You have unlocked free delivery in Dhaka') }}</span>'
                        : '{{ __('Add') }} <span class=\'font-semibold text-ink\'>{{ setting('currency_symbol', '৳') }}' + Math.max(0, threshold - parseFloat(String($store.cart.subtotal).replace(/[^0-9.]/g,''))).toLocaleString() + '</span> {{ __('more for free delivery in Dhaka') }}'"></p>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-ink/10">
                        <div class="h-full rounded-full bg-gradient-to-r from-marigold-400 to-terracotta-500 transition-all duration-700 ease-out-expo" :style="`width:${Math.min(100, parseFloat(String($store.cart.subtotal).replace(/[^0-9.]/g,'')) / threshold * 100)}%`"></div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Items --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5" :class="{ 'opacity-60 pointer-events-none': $store.cart.loading }">
            <template x-if="$store.cart.items.length === 0">
                <div class="flex h-full flex-col items-center justify-center text-center py-16">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-plum-100 text-plum-700 mb-5 animate-float"><x-icon name="bag" class="h-9 w-9" /></div>
                    <p class="font-display text-2xl">{{ __('Nothing in here yet') }}</p>
                    <p class="mt-2 text-ink-500 max-w-[240px]">{{ __('Good things take time. Start with something new.') }}</p>
                    <a href="{{ route('shop.new') }}" class="btn btn-primary btn-md mt-6" @click="$store.ui.cartOpen = false">{{ __('Shop new arrivals') }}</a>
                </div>
            </template>

            <template x-for="item in $store.cart.items" :key="item.id">
                <div class="flex gap-4">
                    <a :href="item.url" class="h-28 w-24 shrink-0 overflow-hidden rounded-2xl bg-paper-200">
                        <img :src="item.image" :alt="item.name" class="h-full w-full object-cover">
                    </a>
                    <div class="flex flex-1 flex-col min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <a :href="item.url" class="block truncate font-semibold text-[15px] hover:text-plum-700" x-text="item.name"></a>
                                <p class="text-sm text-ink-300" x-text="item.variant" x-show="item.variant"></p>
                            </div>
                            <button class="text-ink-300 hover:text-terracotta-600 transition shrink-0" @click="$store.cart.remove(item.id)" aria-label="{{ __('Remove') }}"><x-icon name="trash" class="h-4.5 w-4.5" /></button>
                        </div>
                        <div class="mt-auto flex items-center justify-between">
                            <div class="inline-flex items-center rounded-full border border-ink/10 bg-white">
                                <button class="flex h-9 w-9 items-center justify-center text-ink-500 hover:text-ink disabled:opacity-30" @click="$store.cart.update(item.id, item.quantity - 1)"><x-icon name="minus" class="h-3.5 w-3.5" /></button>
                                <span class="w-6 text-center text-sm font-semibold" x-text="item.quantity"></span>
                                <button class="flex h-9 w-9 items-center justify-center text-ink-500 hover:text-ink disabled:opacity-30" :disabled="item.quantity >= item.max" @click="$store.cart.update(item.id, item.quantity + 1)"><x-icon name="plus" class="h-3.5 w-3.5" /></button>
                            </div>
                            <span class="font-semibold" x-text="item.total"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="border-t border-ink/5 bg-white px-6 py-5 space-y-4" x-show="$store.cart.items.length > 0">
            <div class="space-y-1.5 text-[15px]">
                <div class="flex justify-between text-ink-500"><span>{{ __('Subtotal') }}</span><span x-text="$store.cart.subtotal"></span></div>
                <div class="flex justify-between text-teal-700" x-show="$store.cart.coupon"><span x-text="'{{ __('Coupon') }} ' + $store.cart.coupon"></span><span x-text="'−' + $store.cart.discount"></span></div>
                <div class="flex justify-between text-lg font-bold"><span>{{ __('Total') }}</span><span x-text="$store.cart.total"></span></div>
                <p class="text-xs text-ink-300">{{ __('Delivery calculated at checkout') }}</p>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg w-full" data-magnetic>{{ __('Checkout') }} <x-icon name="arrow-right" class="h-5 w-5" /></a>
            <a href="{{ route('cart.index') }}" class="block text-center text-sm font-semibold text-ink-500 hover:text-ink" @click="$store.ui.cartOpen = false">{{ __('View full bag') }}</a>
        </div>
    </aside>
</div>
