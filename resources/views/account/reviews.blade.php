<x-layouts.app :title="__('My reviews')">
    <section class="relative overflow-hidden bg-plum-950 text-paper">
        <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
        <div class="container relative py-10 md:py-12">
            <h1 class="font-display text-display-sm">{{ __('Reviews') }}</h1>
            <p class="mt-2 text-paper/70">{{ __('What you\'ve said about the pieces you own.') }}</p>
        </div>
    </section>

    <section class="container py-10 md:py-14">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-3">@include('partials.account-nav')</div>
            <div class="lg:col-span-9 space-y-4">
                @forelse($reviews as $review)
                    <article class="rounded-3xl bg-white p-5 md:p-6 ring-1 ring-ink/5 flex gap-4">
                        @if($review->product)
                            <a href="{{ $review->product->url }}" class="h-20 w-16 shrink-0 overflow-hidden rounded-xl bg-paper-200">
                                <img src="{{ $review->product->image_url }}" alt="" class="h-full w-full object-cover">
                            </a>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <a href="{{ $review->product?->url }}" class="font-semibold hover:text-plum-700">{{ $review->product?->name ?? __('Product removed') }}</a>
                                <span class="badge capitalize {{ $review->status === 'approved' ? 'bg-teal-50 text-teal-700' : ($review->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">{{ __($review->status) }}</span>
                            </div>
                            <div class="mt-1"><x-rating :value="$review->rating" size="h-3.5 w-3.5" /></div>
                            @if($review->title)<p class="mt-2 font-semibold">{{ $review->title }}</p>@endif
                            <p class="mt-1 text-sm text-ink-500 leading-relaxed">{{ $review->body }}</p>
                            <p class="mt-2 text-xs text-ink-300">{{ $review->created_at->format('d M Y') }}</p>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border-2 border-dashed border-ink/10 p-12 text-center">
                        <p class="font-display text-2xl">{{ __('No reviews yet') }}</p>
                        <p class="mt-2 text-ink-500">{{ __('After you receive an order, you can leave a review from the product page.') }}</p>
                    </div>
                @endforelse
                <div>{{ $reviews->links() }}</div>
            </div>
        </div>
    </section>
</x-layouts.app>
