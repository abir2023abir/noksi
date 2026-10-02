<x-layouts.app :title="__('Wishlist')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-10 md:py-12">
            <h1 class="font-display text-display-sm">{{ __('Wishlist') }}</h1>
            <p class="mt-2 text-paper/70">{{ __('Pieces you\'re keeping an eye on.') }}</p>
        </div>
    </section>

    <section class="container py-10 md:py-14">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-3">@include('partials.account-nav')</div>
            <div class="lg:col-span-9">
                @if($products->isEmpty())
                    <div class="rounded-3xl border-2 border-dashed border-ink/10 p-12 text-center">
                        <x-icon name="heart" class="mx-auto h-10 w-10 text-ink-300" />
                        <p class="mt-4 font-display text-2xl">{{ __('Nothing saved yet') }}</p>
                        <p class="mt-2 text-ink-500">{{ __('Tap the heart on any product to save it here.') }}</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-md mt-6">{{ __('Explore the shop') }}</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 md:gap-x-6" data-stagger>
                        @foreach($products as $p)
                            <x-product-card :product="$p" data-stagger-item />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.app>
