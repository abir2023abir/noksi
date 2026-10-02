<x-layouts.app :title="__('My orders')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-10 md:py-12">
            <h1 class="font-display text-display-sm">{{ __('Orders') }}</h1>
            <p class="mt-2 text-paper/70">{{ __('Every piece you\'ve brought home.') }}</p>
        </div>
    </section>

    <section class="container py-10 md:py-14">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-3">@include('partials.account-nav')</div>
            <div class="lg:col-span-9 space-y-5">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('account.orders') }}" class="pill {{ !request('status') ? 'bg-plum-950 text-paper' : 'bg-white text-ink' }}">{{ __('All') }}</a>
                    @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                        <a href="{{ route('account.orders', ['status' => $s]) }}" class="pill capitalize {{ request('status') === $s ? 'bg-plum-950 text-paper' : 'bg-white text-ink' }}">{{ __($s) }}</a>
                    @endforeach
                </div>

                @forelse($orders as $order)
                    <a href="{{ route('account.orders.show', $order) }}" class="block rounded-3xl bg-white p-5 md:p-6 ring-1 ring-ink/5 hover:ring-plum-300 transition">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-mono font-semibold">{{ $order->order_number }}</p>
                                <p class="text-sm text-ink-500 mt-0.5">{{ $order->created_at->format('d M Y · h:i A') }}</p>
                            </div>
                            <span class="badge capitalize bg-{{ $order->status_color }}-50 text-{{ $order->status_color }}-700">{{ __($order->status) }}</span>
                        </div>
                        <div class="mt-4 flex items-center gap-4">
                            <div class="flex -space-x-2">
                                @foreach($order->items->take(4) as $item)
                                    <img src="{{ $item->image_url }}" alt="" class="h-14 w-12 rounded-xl object-cover ring-2 ring-white">
                                @endforeach
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-ink-500">{{ $order->items_count }} {{ __('items') }} · {{ $order->payment_label }}</p>
                                <p class="font-semibold mt-0.5">{{ money($order->total) }}</p>
                            </div>
                            <x-icon name="chevron-right" class="h-5 w-5 text-ink-300 shrink-0" />
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border-2 border-dashed border-ink/10 p-12 text-center">
                        <x-icon name="package" class="mx-auto h-10 w-10 text-ink-300" />
                        <p class="mt-4 font-display text-2xl">{{ __('No orders here') }}</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-md mt-5">{{ __('Browse the shop') }}</a>
                    </div>
                @endforelse

                <div>{{ $orders->links() }}</div>
            </div>
        </div>
    </section>
</x-layouts.app>
