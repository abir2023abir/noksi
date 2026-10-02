@if($products->isNotEmpty())
    <div class="flex items-center justify-between mb-6 text-sm text-ink-500">
        <p>{{ __('Showing :from–:to of :total', ['from' => bn_number($products->firstItem()), 'to' => bn_number($products->lastItem()), 'total' => bn_number($products->total())]) }}</p>
    </div>
    <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:gap-x-6 lg:grid-cols-3" data-stagger>
        @foreach($products as $product)
            <x-product-card :product="$product" class="reveal is-visible" />
        @endforeach
    </div>
    <div class="mt-14">{{ $products->links() }}</div>
@else
    <x-empty-state icon="search" :title="__('Nothing matches those filters')" :text="__('Try clearing a filter or two, or browse everything.')" :action="route('shop.index')" :actionText="__('Browse all')" />
@endif
