@php
    $variantsJson = $product->variants->map(fn ($v) => [
        'id' => $v->id, 'size' => $v->size, 'color' => $v->color, 'color_code' => $v->color_code,
        'stock' => (int) $v->stock, 'image' => $v->image_url,
        'price_formatted' => money($flashSale ? $flashSale->sale_price : $v->effective_price),
    ]);
    $price = $product->current_price;
    $original = $product->original_price;
@endphp
<div class="grid md:grid-cols-2" x-data="productOptions({ productId: {{ $product->id }}, variants: @js($variantsJson), price: @js(money($price)), defaultColor: @js($product->variants->first()?->color) })">
    <div class="relative aspect-[3/4] md:aspect-auto md:min-h-[560px] bg-paper-200 overflow-hidden rounded-t-4xl md:rounded-l-4xl md:rounded-tr-none">
        @foreach($product->images as $i => $img)
            <img src="{{ $img->url }}" alt="{{ $product->name }}" x-show="galleryIndex === {{ $i }}" x-transition.opacity class="absolute inset-0 h-full w-full object-cover">
        @endforeach
        @if($product->images->count() > 1)
            <div class="absolute bottom-4 inset-x-0 flex justify-center gap-1.5">
                @foreach($product->images as $i => $img)
                    <button type="button" @click="galleryIndex = {{ $i }}" class="h-2 rounded-full bg-white transition-all" :class="galleryIndex === {{ $i }} ? 'w-7' : 'w-2 opacity-60'"></button>
                @endforeach
            </div>
        @endif
    </div>
    <div class="p-6 md:p-8 space-y-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-300">{{ $product->category?->name }}</p>
            <h2 class="mt-1 font-display text-3xl leading-tight">{{ $product->name }}</h2>
            <div class="mt-3 flex items-baseline gap-3">
                <span class="text-2xl font-bold {{ $original ? 'text-terracotta-700' : '' }}" x-text="price">{{ money($price) }}</span>
                @if($original)<span class="text-ink-300 line-through">{{ money($original) }}</span>@endif
            </div>
            @if($product->rating_count)<div class="mt-2 flex items-center gap-2 text-sm text-ink-500"><x-rating :value="$product->rating_avg" size="h-3.5 w-3.5" /> {{ $product->rating_count }} {{ __('reviews') }}</div>@endif
        </div>
        <p class="text-ink-500 text-[15px] leading-relaxed line-clamp-3">{{ $product->short_description }}</p>

        <template x-if="colors.length">
            <div>
                <p class="mb-2 text-sm font-semibold">{{ __('Colour') }}: <span class="font-normal text-ink-500" x-text="color"></span></p>
                <div class="flex flex-wrap gap-2">
                    <template x-for="c in colors" :key="c.name">
                        <button type="button" @click="selectColor(c.name)" class="h-9 w-9 rounded-full border border-ink/10 ring-2 ring-offset-2 ring-offset-paper transition" :class="color === c.name ? 'ring-plum-950' : 'ring-transparent'" :style="`background:${c.code || '#ccc'}`" :title="c.name"></button>
                    </template>
                </div>
            </div>
        </template>
        <template x-if="sizes.length">
            <div>
                <p class="mb-2 text-sm font-semibold">{{ __('Size') }}: <span class="font-normal text-ink-500" x-text="size || '{{ __('Select') }}'"></span></p>
                <div class="flex flex-wrap gap-2">
                    <template x-for="s in sizes" :key="s">
                        <button type="button" @click="size = s" :disabled="stockFor(s) === 0" class="flex h-11 min-w-[48px] items-center justify-center rounded-xl border px-3 text-sm font-semibold transition disabled:opacity-40 disabled:line-through" :class="size === s ? 'bg-plum-950 text-paper border-plum-950' : 'bg-white border-ink/15 hover:border-ink'" x-text="s"></button>
                    </template>
                </div>
            </div>
        </template>

        <div class="flex gap-3">
            <div class="inline-flex items-center rounded-full border border-ink/15 bg-white h-12">
                <button type="button" class="flex h-full w-10 items-center justify-center" @click="qty = Math.max(1, qty - 1)"><x-icon name="minus" class="h-4 w-4" /></button>
                <span class="w-6 text-center font-semibold" x-text="qty"></span>
                <button type="button" class="flex h-full w-10 items-center justify-center" @click="qty = Math.min(maxQty, qty + 1)"><x-icon name="plus" class="h-4 w-4" /></button>
            </div>
            @if($product->is_in_stock)
                <button type="button" class="btn btn-primary btn-md flex-1" @click="add()"><x-icon name="bag" class="h-4.5 w-4.5" /> <span x-text="(variants.length && !selected) ? '{{ __('Select options') }}' : '{{ __('Add to bag') }}'"></span></button>
            @else
                <button type="button" class="btn btn-outline btn-md flex-1" disabled>{{ __('Sold out') }}</button>
            @endif
        </div>
        <a href="{{ $product->url }}" class="inline-flex items-center gap-1 text-sm font-semibold text-plum-700 link-underline">{{ __('View full details') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
    </div>
</div>
