<x-layouts.app :title="__('Track order')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-12 md:py-16">
            <x-breadcrumb :items="[__('Track order') => null]" class="text-paper/60 [&_a]:text-paper/60 [&_a:hover]:text-paper [&_span]:text-paper" />
            <h1 class="mt-4 font-display text-display-sm md:text-display-md" data-split>{{ __('Track your order') }}</h1>
            <p class="mt-2 text-paper/70 max-w-xl">{{ __('Enter your order number — it starts with NK and was in your confirmation SMS.') }}</p>
        </div>
    </section>

    <section class="container py-10 md:py-14">
        <form method="GET" action="{{ route('order.track.index') }}" class="mx-auto max-w-xl">
            <div class="flex gap-3">
                <input type="text" name="number" value="{{ $order?->order_number ?? ($notFound ?? '') }}" class="input flex-1 uppercase tracking-wider font-mono" placeholder="NK260930XXXXX" required>
                <button class="btn btn-primary btn-md shrink-0">{{ __('Track') }}</button>
            </div>
        </form>

        @isset($notFound)
            <div class="mx-auto mt-10 max-w-xl">
                <x-alert type="error">{{ __('We couldn\'t find order :n. Double-check the number and try again.', ['n' => $notFound]) }}</x-alert>
            </div>
        @endisset

        @if($order)
            @php
                $steps = [
                    'pending' => __('Placed'),
                    'confirmed' => __('Confirmed'),
                    'processing' => __('Packing'),
                    'shipped' => __('Shipped'),
                    'delivered' => __('Delivered'),
                ];
                $progress = $order->progress;
                $isCancelled = in_array($order->status, ['cancelled', 'refunded', 'returned']);
            @endphp

            <div class="mx-auto mt-12 max-w-3xl space-y-8">
                <div class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-ink-300">{{ __('Order') }}</p>
                            <p class="font-display text-3xl tracking-wide">{{ $order->order_number }}</p>
                            <p class="mt-1 text-sm text-ink-500">{{ __('Placed') }} {{ $order->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <span class="badge capitalize bg-{{ $order->status_color }}-50 text-{{ $order->status_color }}-700 text-sm px-3 py-1.5">{{ __($order->status) }}</span>
                    </div>

                    @unless($isCancelled)
                        <div class="mt-10 relative">
                            <div class="absolute left-0 right-0 top-4 h-1 bg-ink/10 rounded-full">
                                <div class="h-full rounded-full bg-gradient-to-r from-plum-700 to-teal-600 transition-all duration-700" style="width: {{ max(0, ($progress - 1) / 4 * 100) }}%"></div>
                            </div>
                            <ol class="relative grid grid-cols-5 gap-1">
                                @foreach($steps as $key => $label)
                                    @php $i = array_search($key, array_keys($steps)) + 1; $done = $progress >= $i; $current = $progress === $i; @endphp
                                    <li class="flex flex-col items-center text-center">
                                        <span class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold ring-4 ring-paper transition
                                            {{ $done ? 'bg-plum-950 text-paper' : 'bg-ink/10 text-ink-300' }}
                                            {{ $current ? 'scale-110' : '' }}">
                                            @if($done && !$current)<x-icon name="check" class="h-4 w-4" />@else{{ $i }}@endif
                                        </span>
                                        <span class="mt-3 text-[11px] md:text-xs font-semibold {{ $done ? 'text-ink' : 'text-ink-300' }}">{{ $label }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @else
                        <p class="mt-6 rounded-2xl bg-terracotta-50 px-4 py-3 text-sm text-terracotta-800">
                            {{ __('This order was :status.', ['status' => __($order->status)]) }}
                            @if($order->histories->first()?->comment) — {{ $order->histories->first()->comment }}@endif
                        </p>
                    @endunless

                    @if($order->tracking_number)
                        <div class="mt-8 flex flex-wrap items-center gap-3 rounded-2xl bg-paper p-4">
                            <x-icon name="truck" class="h-5 w-5 text-teal-700" />
                            <div>
                                <p class="text-sm text-ink-500">{{ $order->courier ? __(':c tracking', ['c' => $order->courier]) : __('Courier tracking') }}</p>
                                <p class="font-mono font-semibold">{{ $order->tracking_number }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5">
                        <h2 class="font-display text-xl mb-4">{{ __('Items') }}</h2>
                        <ul class="space-y-3">
                            @foreach($order->items as $item)
                                <li class="flex gap-3">
                                    <img src="{{ $item->image_url }}" alt="" class="h-14 w-12 rounded-lg object-cover bg-paper-200 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold truncate">{{ $item->name }}</p>
                                        <p class="text-xs text-ink-300">× {{ $item->quantity }} · {{ money($item->total) }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 border-t border-ink/10 pt-3 flex justify-between font-bold">
                            <span>{{ __('Total') }}</span><span>{{ money($order->total) }}</span>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-white p-6 ring-1 ring-ink/5">
                        <h2 class="font-display text-xl mb-4">{{ __('Timeline') }}</h2>
                        @if($order->histories->isNotEmpty())
                            <ol class="space-y-4">
                                @foreach($order->histories as $h)
                                    <li class="flex gap-3">
                                        <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-plum-700"></span>
                                        <div>
                                            <p class="font-semibold capitalize text-sm">{{ __($h->status) }}</p>
                                            @if($h->comment)<p class="text-sm text-ink-500">{{ $h->comment }}</p>@endif
                                            <p class="text-xs text-ink-300 mt-0.5">{{ $h->created_at->format('d M Y · h:i A') }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <p class="text-sm text-ink-500">{{ __('Order placed') }} — {{ $order->created_at->format('d M Y · h:i A') }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="{{ route('contact') }}" class="btn btn-outline btn-md"><x-icon name="message" class="h-4.5 w-4.5" /> {{ __('Need help?') }}</a>
                    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-md">{{ __('Continue shopping') }}</a>
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>
