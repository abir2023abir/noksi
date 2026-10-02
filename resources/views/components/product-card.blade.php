@props(['product', 'eager' => false])
@php
    $price = $product->current_price;
    $original = $product->original_price;
    $discount = $product->discount_percent;
    $hover = $product->hover_image_url;
@endphp
<article {{ $attributes->merge(['class' => 'product-card group']) }}>
    <div class="product-card__media">
        <a href="{{ $product->url }}" class="absolute inset-0" aria-label="{{ $product->name }}">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-card__img" @if(!$eager) loading="lazy" @endif>
            @if($hover)
                <img src="{{ $hover }}" alt="" class="product-card__hover" loading="lazy" aria-hidden="true">
            @endif
        </a>

        {{-- Badges --}}
        <div class="absolute left-3 top-3 flex flex-col gap-1.5">
            @if($discount > 0)
                <span class="badge bg-terracotta-600 text-white">−{{ $discount }}%</span>
            @endif
            @if($product->badge)
                <span class="badge text-white" style="background: {{ $product->badge_color ?? '#5B1B4F' }}">{{ $product->badge }}</span>
            @elseif($product->is_new)
                <span class="badge bg-marigold-400 text-plum-950">{{ __('New') }}</span>
            @endif
            @if(!$product->is_in_stock)
                <span class="badge bg-ink text-paper">{{ __('Sold out') }}</span>
            @endif
        </div>

        {{-- Wishlist --}}
        <button type="button" @click.prevent="$store.wishlist.toggle({{ $product->id }})"
                class="absolute right-3 top-3 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 backdrop-blur text-ink shadow-soft transition hover:scale-110 hover:bg-white"
                :class="{ 'text-terracotta-600': $store.wishlist.has({{ $product->id }}) }" aria-label="{{ __('Wishlist') }}">
            <template x-if="$store.wishlist.has({{ $product->id }})"><x-icon name="heart-fill" class="h-5 w-5" /></template>
            <template x-if="!$store.wishlist.has({{ $product->id }})"><x-icon name="heart" class="h-5 w-5" /></template>
        </button>

        {{-- Quick actions --}}
        <div class="absolute inset-x-3 bottom-3 flex gap-2 translate-y-4 opacity-0 transition duration-500 ease-out-expo group-hover:translate-y-0 group-hover:opacity-100">
            <button type="button" @click="$dispatch('quick-view', '{{ $product->slug }}')" class="btn btn-white btn-sm flex-1 !rounded-2xl shadow-lift">
                <x-icon name="eye" class="h-4 w-4" /> {{ __('Quick view') }}
            </button>
            @if($product->is_in_stock && !$product->has_variants)
                <button type="button" @click="$store.cart.add({{ $product->id }})" class="btn btn-primary btn-sm !rounded-2xl !px-3.5 shadow-lift" aria-label="{{ __('Add to bag') }}">
                    <x-icon name="bag" class="h-4.5 w-4.5" />
                </button>
            @endif
        </div>
    </div>

    <div class="mt-4 flex items-start justify-between gap-3">
        <div class="min-w-0">
            @if($product->category)
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-300">{{ $product->category->name }}</p>
            @endif
            <h3 class="mt-0.5 font-semibold text-[15px] leading-snug text-ink">
                <a href="{{ $product->url }}" class="hover:text-plum-700 transition">{{ app()->getLocale() === 'bn' && $product->name_bn ? $product->name_bn : $product->name }}</a>
            </h3>
            @if($product->rating_count > 0)
                <div class="mt-1 flex items-center gap-1.5 text-xs text-ink-500">
                    <x-rating :value="$product->rating_avg" size="h-3 w-3" />
                    <span>({{ $product->rating_count }})</span>
                </div>
            @endif
        </div>
        <div class="text-right shrink-0">
            <p class="font-bold text-[15px] {{ $original ? 'text-terracotta-700' : '' }}">{{ money($price) }}</p>
            @if($original)
                <p class="text-xs text-ink-300 line-through">{{ money($original) }}</p>
            @endif
        </div>
    </div>
</article>
