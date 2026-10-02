<x-layouts.app :title="__('My account')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-10 md:py-14">
            <p class="eyebrow text-marigold-400">{{ __('Account') }}</p>
            <h1 class="mt-2 font-display text-display-sm md:text-display-md">{{ __('Hello, :name', ['name' => explode(' ', $user->name)[0]]) }}</h1>
            <p class="mt-2 text-paper/70">{{ __('Orders, wishlist, and the bits that make shopping easier.') }}</p>
        </div>
    </section>

    <section class="container py-10 md:py-14">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-3">@include('partials.account-nav')</div>

            <div class="lg:col-span-9 space-y-8">
                <div class="grid gap-4 sm:grid-cols-3">
                    <a href="{{ route('account.orders') }}" class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 hover:ring-plum-300 transition group">
                        <x-icon name="package" class="h-6 w-6 text-plum-700" />
                        <p class="mt-4 font-display text-4xl">{{ $ordersCount }}</p>
                        <p class="mt-1 text-sm text-ink-500 group-hover:text-plum-700">{{ __('Orders') }}</p>
                    </a>
                    <a href="{{ route('account.wishlist') }}" class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 hover:ring-terracotta-300 transition group">
                        <x-icon name="heart" class="h-6 w-6 text-terracotta-600" />
                        <p class="mt-4 font-display text-4xl">{{ $wishlistCount }}</p>
                        <p class="mt-1 text-sm text-ink-500 group-hover:text-terracotta-700">{{ __('Saved items') }}</p>
                    </a>
                    <a href="{{ route('account.addresses.index') }}" class="rounded-3xl bg-white p-6 ring-1 ring-ink/5 hover:ring-teal-300 transition group">
                        <x-icon name="map-pin" class="h-6 w-6 text-teal-700" />
                        <p class="mt-4 font-display text-lg leading-snug truncate">{{ $address?->district ?? __('No address') }}</p>
                        <p class="mt-1 text-sm text-ink-500 group-hover:text-teal-700">{{ __('Default delivery') }}</p>
                    </a>
                </div>

                <div class="rounded-3xl bg-white p-6 md:p-8 ring-1 ring-ink/5">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="font-display text-2xl">{{ __('Recent orders') }}</h2>
                        <a href="{{ route('account.orders') }}" class="text-sm font-semibold text-plum-700 hover:underline">{{ __('View all') }}</a>
                    </div>
                    @forelse($recentOrders as $order)
                        <a href="{{ route('account.orders.show', $order) }}" class="flex items-center gap-4 py-4 border-t border-ink/5 first:border-0 first:pt-0 hover:bg-paper/50 -mx-2 px-2 rounded-xl transition">
                            <div class="flex -space-x-2 shrink-0">
                                @foreach($order->items->take(3) as $item)
                                    <img src="{{ $item->image_url }}" alt="" class="h-12 w-10 rounded-lg object-cover ring-2 ring-white">
                                @endforeach
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold font-mono text-sm">{{ $order->order_number }}</p>
                                <p class="text-sm text-ink-500">{{ $order->created_at->format('d M Y') }} · {{ $order->items_count }} {{ __('items') }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-semibold">{{ money($order->total) }}</p>
                                <span class="badge capitalize bg-{{ $order->status_color }}-50 text-{{ $order->status_color }}-700">{{ __($order->status) }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-10">
                            <p class="text-ink-500">{{ __('No orders yet.') }}</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary btn-md mt-4">{{ __('Start shopping') }}</a>
                        </div>
                    @endforelse
                </div>

                @if($picks->isNotEmpty())
                    <div>
                        <x-section-heading eyebrow="{{ __('Picked for you') }}" title="{{ __('Keep exploring') }}" :link="route('shop.index')" />
                        <div class="mt-8 grid grid-cols-2 gap-x-4 gap-y-8 md:grid-cols-4 md:gap-x-5">
                            @foreach($picks as $p)<x-product-card :product="$p" />@endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.app>
