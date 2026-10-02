<x-layouts.app :title="__('Order').' '.$order->order_number">
    <section class="container py-10 md:py-14">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-3">@include('partials.account-nav')</div>
            <div class="lg:col-span-9 space-y-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <a href="{{ route('account.orders') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-plum-700 hover:underline mb-2"><x-icon name="arrow-left" class="h-4 w-4" /> {{ __('Orders') }}</a>
                        <h1 class="font-display text-3xl tracking-wide">{{ $order->order_number }}</h1>
                        <p class="mt-1 text-ink-500">{{ __('Placed') }} {{ $order->created_at->format('d M Y · h:i A') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="badge capitalize bg-{{ $order->status_color }}-50 text-{{ $order->status_color }}-700 text-sm px-3 py-1.5">{{ __($order->status) }}</span>
                        <a href="{{ route('account.orders.invoice', $order) }}" class="btn btn-outline btn-sm"><x-icon name="printer" class="h-4 w-4" /> {{ __('Invoice') }}</a>
                        <a href="{{ route('order.track', $order->order_number) }}" class="btn btn-outline btn-sm"><x-icon name="truck" class="h-4 w-4" /> {{ __('Track') }}</a>
                    </div>
                </div>

                @php
                    $steps = ['pending'=>__('Placed'),'confirmed'=>__('Confirmed'),'processing'=>__('Packing'),'shipped'=>__('Shipped'),'delivered'=>__('Delivered')];
                    $progress = $order->progress;
                @endphp
                @unless(in_array($order->status, ['cancelled','refunded','returned']))
                    <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5">
                        <div class="relative">
                            <div class="absolute left-0 right-0 top-4 h-1 bg-ink/10 rounded-full">
                                <div class="h-full rounded-full bg-gradient-to-r from-plum-700 to-teal-600" style="width: {{ max(0, ($progress - 1) / 4 * 100) }}%"></div>
                            </div>
                            <ol class="relative grid grid-cols-5 gap-1">
                                @foreach($steps as $key => $label)
                                    @php $i = array_search($key, array_keys($steps)) + 1; $done = $progress >= $i; @endphp
                                    <li class="flex flex-col items-center text-center">
                                        <span class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold ring-4 ring-white {{ $done ? 'bg-plum-950 text-paper' : 'bg-ink/10 text-ink-300' }}">
                                            @if($done && $progress !== $i)<x-icon name="check" class="h-4 w-4" />@else{{ $i }}@endif
                                        </span>
                                        <span class="mt-2 text-[11px] font-semibold {{ $done ? 'text-ink' : 'text-ink-300' }}">{{ $label }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                @endunless

                <div class="grid gap-6 md:grid-cols-5">
                    <div class="md:col-span-3 rounded-3xl bg-white p-6 ring-1 ring-ink/5">
                        <h2 class="font-display text-xl mb-4">{{ __('Items') }}</h2>
                        <ul class="space-y-4">
                            @foreach($order->items as $item)
                                <li class="flex gap-4">
                                    <img src="{{ $item->image_url }}" alt="" class="h-20 w-16 rounded-xl object-cover bg-paper-200 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold">{{ $item->name }}</p>
                                        @if($item->variant_label)<p class="text-sm text-ink-300">{{ $item->variant_label }}</p>@endif
                                        <p class="text-sm text-ink-500">× {{ $item->quantity }} · {{ money($item->price) }}</p>
                                    </div>
                                    <span class="font-semibold shrink-0">{{ money($item->total) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-5 border-t border-ink/10 pt-4 space-y-2 text-sm">
                            <div class="flex justify-between text-ink-500"><span>{{ __('Subtotal') }}</span><span>{{ money($order->subtotal) }}</span></div>
                            @if($order->discount > 0)<div class="flex justify-between text-teal-700"><span>{{ __('Discount') }}</span><span>−{{ money($order->discount) }}</span></div>@endif
                            <div class="flex justify-between text-ink-500"><span>{{ __('Delivery') }}</span><span>{{ $order->shipping_cost > 0 ? money($order->shipping_cost) : __('Free') }}</span></div>
                            <div class="flex justify-between text-lg font-bold pt-2"><span>{{ __('Total') }}</span><span>{{ money($order->total) }}</span></div>
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-5">
                        <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 text-sm space-y-3">
                            <h2 class="font-display text-xl">{{ __('Shipping') }}</h2>
                            @php $a = $order->shipping_address; @endphp
                            <p class="font-semibold text-base">{{ $a['name'] ?? $order->customer_name }}</p>
                            <p class="text-ink-500">{{ $a['phone'] ?? $order->customer_phone }}</p>
                            <p class="text-ink-500">{{ $a['address_line'] ?? '' }}@if(!empty($a['area'])), {{ $a['area'] }}@endif</p>
                            <p class="text-ink-500">{{ $a['city'] ?? '' }}, {{ $a['district'] ?? '' }}</p>
                            @if($order->shippingMethod)<p class="text-ink-500 pt-2 border-t border-ink/5">{{ $order->shippingMethod->name }}</p>@endif
                            @if($order->tracking_number)<p class="font-mono font-semibold pt-1">{{ $order->tracking_number }}</p>@endif
                        </div>
                        <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 text-sm space-y-2">
                            <h2 class="font-display text-xl mb-2">{{ __('Payment') }}</h2>
                            <div class="flex justify-between"><span class="text-ink-500">{{ __('Method') }}</span><span class="font-semibold">{{ $order->payment_label }}</span></div>
                            <div class="flex justify-between"><span class="text-ink-500">{{ __('Status') }}</span><span class="capitalize font-semibold">{{ __($order->payment_status) }}</span></div>
                        </div>

                        @if($order->can_cancel)
                            <form method="POST" action="{{ route('account.orders.cancel', $order) }}" onsubmit="return confirm(@js(__('Cancel this order?')))">
                                @csrf
                                <button class="btn btn-outline btn-md w-full text-terracotta-700 border-terracotta-300 hover:bg-terracotta-50">{{ __('Cancel order') }}</button>
                            </form>
                        @endif
                        @if($order->payment_method === 'sslcommerz' && $order->payment_status !== 'paid')
                            <a href="{{ route('checkout.pay', $order) }}" class="btn btn-primary btn-md w-full">{{ __('Complete payment') }}</a>
                        @endif
                    </div>
                </div>

                @if($order->histories->isNotEmpty())
                    <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5">
                        <h2 class="font-display text-xl mb-4">{{ __('Timeline') }}</h2>
                        <ol class="space-y-4">
                            @foreach($order->histories as $h)
                                <li class="flex gap-3">
                                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-plum-700"></span>
                                    <div>
                                        <p class="font-semibold capitalize text-sm">{{ __($h->status) }}</p>
                                        @if($h->comment)<p class="text-sm text-ink-500">{{ $h->comment }}</p>@endif
                                        <p class="text-xs text-ink-300">{{ $h->created_at->format('d M Y · h:i A') }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.app>
