<x-layouts.app :title="__('Order confirmed')">
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-teal-900 via-plum-950 to-plum-900"></div>
        <div class="absolute inset-0 grain opacity-40 pointer-events-none"></div>
        <div class="container relative py-16 md:py-24 text-center text-paper">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-marigold-400 text-plum-950 mb-6 animate-float">
                <x-icon name="check" class="h-10 w-10" />
            </div>
            <p class="eyebrow text-marigold-400">{{ __('Thank you') }}</p>
            <h1 class="mt-3 font-display text-display-sm md:text-display-md text-balance" data-split>{{ __('Your order is in') }}</h1>
            <p class="mt-4 text-lg text-paper/70 max-w-xl mx-auto">
                {{ __('We\'ve got it. A confirmation is on its way to') }}
                <span class="text-paper font-semibold">{{ $order->shipping_address['phone'] ?? $order->email ?? __('your phone') }}</span>.
            </p>
            <div class="mt-8 inline-flex flex-col sm:flex-row items-center gap-3 rounded-3xl bg-white/10 backdrop-blur px-6 py-4 ring-1 ring-white/15">
                <span class="text-sm text-paper/60">{{ __('Order number') }}</span>
                <span class="font-display text-2xl tracking-wide">{{ $order->order_number }}</span>
                <button type="button" class="text-marigold-400 hover:text-marigold-300" @click="navigator.clipboard.writeText(@js($order->order_number)); $store.ui.toast(@js(__('Copied')))" aria-label="{{ __('Copy') }}">
                    <x-icon name="copy" class="h-5 w-5" />
                </button>
            </div>
        </div>
    </section>

    <section class="container py-12 md:py-16">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-7 space-y-6">
                <div class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5">
                    <h2 class="font-display text-2xl mb-5">{{ __('Order details') }}</h2>
                    <ul class="space-y-4">
                        @foreach($order->items as $item)
                            <li class="flex gap-4">
                                <img src="{{ $item->image_url }}" alt="" class="h-20 w-16 rounded-xl object-cover bg-paper-200 shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold">{{ $item->name }}</p>
                                    @if($item->variant_label)<p class="text-sm text-ink-300">{{ $item->variant_label }}</p>@endif
                                    <p class="text-sm text-ink-500">× {{ $item->quantity }}</p>
                                </div>
                                <span class="font-semibold shrink-0">{{ money($item->total) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-6 border-t border-ink/10 pt-4 space-y-2 text-[15px]">
                        <div class="flex justify-between text-ink-500"><span>{{ __('Subtotal') }}</span><span>{{ money($order->subtotal) }}</span></div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-teal-700"><span>{{ __('Discount') }}</span><span>−{{ money($order->discount) }}</span></div>
                        @endif
                        <div class="flex justify-between text-ink-500"><span>{{ __('Delivery') }}</span><span>{{ $order->shipping_cost > 0 ? money($order->shipping_cost) : __('Free') }}</span></div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-ink/10"><span>{{ __('Total') }}</span><span>{{ money($order->total) }}</span></div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5">
                    <h2 class="font-display text-2xl mb-4">{{ __('What happens next') }}</h2>
                    <ol class="space-y-4">
                        @foreach([
                            [__('Confirmed'), __('We\'ll verify stock and pack your order within a day.')],
                            [__('On the way'), __('Courier picks up — usually 1–2 days inside Dhaka, 3–5 outside.')],
                            [__('Delivered'), $order->payment_method === 'cod' ? __('Pay the courier in cash. Keep some change handy.') : __('Already paid — just open the package and enjoy.')],
                        ] as $i => [$t, $d])
                            <li class="flex gap-4">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-plum-100 font-bold text-plum-700">{{ $i + 1 }}</span>
                                <div><p class="font-semibold">{{ $t }}</p><p class="text-sm text-ink-500">{{ $d }}</p></div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            <aside class="lg:col-span-5 space-y-5">
                <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 space-y-4">
                    <h2 class="font-display text-xl">{{ __('Shipping to') }}</h2>
                    @php $a = $order->shipping_address; @endphp
                    <div class="text-[15px] text-ink-500 space-y-1">
                        <p class="font-semibold text-ink">{{ $a['name'] ?? '' }}</p>
                        <p>{{ $a['phone'] ?? '' }}</p>
                        <p>{{ $a['address_line'] ?? '' }}@if(!empty($a['area'])), {{ $a['area'] }}@endif</p>
                        <p>{{ $a['city'] ?? '' }}, {{ $a['district'] ?? '' }}@if(!empty($a['postal_code'])) {{ $a['postal_code'] }}@endif</p>
                    </div>
                    <div class="border-t border-ink/10 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-ink-500">{{ __('Payment') }}</span><span class="font-semibold">{{ $order->payment_label }}</span></div>
                        <div class="flex justify-between"><span class="text-ink-500">{{ __('Status') }}</span><span class="badge bg-{{ $order->status_color }}-50 text-{{ $order->status_color }}-700 capitalize">{{ __($order->status) }}</span></div>
                        @if($order->shippingMethod)
                            <div class="flex justify-between"><span class="text-ink-500">{{ __('Delivery') }}</span><span class="font-semibold">{{ $order->shippingMethod->name }}</span></div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <a href="{{ route('order.track', $order->order_number) }}" class="btn btn-primary btn-md w-full"><x-icon name="package" class="h-5 w-5" /> {{ __('Track order') }}</a>
                    @auth
                        <a href="{{ route('account.orders.invoice', $order) }}" class="btn btn-outline btn-md w-full"><x-icon name="printer" class="h-5 w-5" /> {{ __('Download invoice') }}</a>
                        <a href="{{ route('account.orders.show', $order) }}" class="btn btn-ghost btn-md w-full">{{ __('View in my account') }}</a>
                    @endauth
                    @if($order->payment_method === 'sslcommerz' && $order->payment_status !== 'paid')
                        <a href="{{ route('checkout.pay', $order) }}" class="btn btn-outline btn-md w-full text-terracotta-700 border-terracotta-300"><x-icon name="credit-card" class="h-5 w-5" /> {{ __('Complete payment') }}</a>
                    @endif
                    <a href="{{ route('shop.index') }}" class="text-center text-sm font-semibold text-plum-700 hover:underline pt-1">{{ __('Continue shopping') }}</a>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
