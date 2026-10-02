@extends('admin.layouts.app')

@section('title', $product->name)

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-display text-3xl">{{ $product->name }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $product->sku }} · {{ $product->category?->name ?? 'Uncategorized' }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary"><x-icon name="edit" class="h-4 w-4" /> Edit</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="adm-card p-6">
            <div class="adm-card-head !px-0 !pt-0"><h2 class="adm-card-title">Details</h2></div>
            <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                <div><dt class="text-ink-300">Price</dt><dd class="font-semibold">{{ money($product->price) }}</dd></div>
                <div><dt class="text-ink-300">Compare</dt><dd>{{ $product->compare_price ? money($product->compare_price) : '—' }}</dd></div>
                <div><dt class="text-ink-300">Stock</dt><dd class="{{ $product->stock <= 5 ? 'text-amber-600 font-semibold' : '' }}">{{ $product->stock }}</dd></div>
                <div><dt class="text-ink-300">Brand</dt><dd>{{ $product->brand?->name ?? '—' }}</dd></div>
                <div><dt class="text-ink-300">Gender</dt><dd>{{ $product->gender ? ucfirst($product->gender) : '—' }}</dd></div>
                <div><dt class="text-ink-300">Material</dt><dd>{{ $product->material ?: '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-ink-300">Status</dt>
                    <dd class="mt-1 flex flex-wrap gap-1">
                        <span class="pill {{ status_badge($product->is_active ? 'active' : 'inactive') }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                        @if($product->is_featured)<span class="pill bg-sky-100 text-sky-800">Featured</span>@endif
                        @if($product->is_new)<span class="pill bg-violet-100 text-violet-800">New</span>@endif
                        @if($product->is_trending)<span class="pill bg-orange-100 text-orange-800">Trending</span>@endif
                    </dd>
                </div>
                @if($product->short_description)
                    <div class="sm:col-span-2"><dt class="text-ink-300">Short</dt><dd>{{ $product->short_description }}</dd></div>
                @endif
                @if($product->description)
                    <div class="sm:col-span-2"><dt class="text-ink-300">Description</dt><dd class="prose prose-sm max-w-none">{!! nl2br(e($product->description)) !!}</dd></div>
                @endif
            </dl>
        </div>

        <div class="adm-card overflow-x-auto">
            <div class="adm-card-head"><h2 class="adm-card-title">Variants ({{ $product->variants->count() }})</h2></div>
            <table class="adm-table">
                <thead><tr><th>Size</th><th>Color</th><th>SKU</th><th>Stock</th><th>Price</th></tr></thead>
                <tbody>
                    @forelse($product->variants as $v)
                        <tr>
                            <td>{{ $v->size ?: '—' }}</td>
                            <td class="flex items-center gap-2">
                                @if($v->color_code)<span class="inline-block h-3 w-3 rounded-full ring-1 ring-ink/10" style="background:{{ $v->color_code }}"></span>@endif
                                {{ $v->color ?: '—' }}
                            </td>
                            <td class="text-xs text-ink-300">{{ $v->sku ?: '—' }}</td>
                            <td>{{ $v->stock }}</td>
                            <td>{{ $v->price !== null ? money($v->price) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-ink-300">No variants</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="adm-card overflow-x-auto">
            <div class="adm-card-head"><h2 class="adm-card-title">Reviews ({{ $product->reviews->count() }})</h2></div>
            <table class="adm-table">
                <thead><tr><th>User</th><th>Rating</th><th>Status</th><th>Comment</th></tr></thead>
                <tbody>
                    @forelse($product->reviews as $r)
                        <tr>
                            <td>{{ $r->user?->name ?? $r->name ?? '—' }}</td>
                            <td>{{ $r->rating }}/5</td>
                            <td><span class="pill {{ status_badge($r->status) }}">{{ $r->status }}</span></td>
                            <td class="max-w-xs truncate text-sm">{{ $r->title ?: Str::limit($r->body, 60) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-ink-300">No reviews</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-6">
        <div class="adm-card p-4">
            <img src="{{ $product->image_url }}" alt="" class="aspect-[3/4] w-full rounded-xl object-cover bg-paper-100">
            @if($product->images->count() > 1)
                <div class="mt-3 grid grid-cols-4 gap-2">
                    @foreach($product->images->take(8) as $image)
                        <img src="{{ img($image->path) }}" alt="" class="aspect-square rounded-lg object-cover bg-paper-100">
                    @endforeach
                </div>
            @endif
        </div>
        @if($product->collections->isNotEmpty())
            <div class="adm-card p-6">
                <h2 class="adm-card-title mb-3">Collections</h2>
                <div class="flex flex-wrap gap-1">
                    @foreach($product->collections as $col)
                        <span class="pill bg-slate-100 text-slate-700">{{ $col->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="adm-card p-6 text-sm space-y-2">
            <p><span class="text-ink-300">Sold:</span> {{ $product->sold_count }}</p>
            <p><span class="text-ink-300">Views:</span> {{ $product->views }}</p>
            <p><span class="text-ink-300">Rating:</span> {{ $product->rating_avg }} ({{ $product->rating_count }})</p>
        </div>
    </div>
</div>
@endsection
