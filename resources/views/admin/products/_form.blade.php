@php
    $product = $product ?? null;
    $selectedCollections = old('collection_ids', $product?->collections?->pluck('id')->all() ?? []);
    $tagsValue = old('tags', is_array($product?->tags) ? implode(', ', $product->tags) : ($product?->tags ?? ''));
    $variants = old('variants', $product?->variants?->map(fn ($v) => [
        'id' => $v->id, 'size' => $v->size, 'color' => $v->color, 'color_code' => $v->color_code,
        'sku' => $v->sku, 'stock' => $v->stock, 'price' => $v->price,
    ])->values()->all() ?? [['size' => '', 'color' => '', 'color_code' => '', 'sku' => '', 'stock' => 0, 'price' => '']]);
@endphp

<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="adm-card p-6 space-y-4">
            <h2 class="adm-card-title">Basics</h2>
            <div>
                <label class="adm-label">Name *</label>
                <input type="text" name="name" value="{{ old('name', $product?->name) }}" class="adm-input" required>
                @error('name')<p class="adm-error">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="adm-label">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $product?->slug) }}" class="adm-input" placeholder="auto">
                    @error('slug')<p class="adm-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="adm-label">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}" class="adm-input" placeholder="auto">
                    @error('sku')<p class="adm-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="adm-label">Category</label>
                    <select name="category_id" class="adm-select">
                        <option value="">—</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('category_id', $product?->category_id) == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="adm-label">Brand</label>
                    <select name="brand_id" class="adm-select">
                        <option value="">—</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" @selected(old('brand_id', $product?->brand_id) == $b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="adm-label">Short description</label>
                <textarea name="short_description" rows="2" class="adm-input">{{ old('short_description', $product?->short_description) }}</textarea>
            </div>
            <div>
                <label class="adm-label">Description</label>
                <textarea name="description" rows="6" class="adm-input">{{ old('description', $product?->description) }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="adm-label">Material</label>
                    <input type="text" name="material" value="{{ old('material', $product?->material) }}" class="adm-input">
                </div>
                <div>
                    <label class="adm-label">Gender</label>
                    <select name="gender" class="adm-select">
                        <option value="">—</option>
                        @foreach(['men','women','unisex','kids'] as $g)
                            <option value="{{ $g }}" @selected(old('gender', $product?->gender) === $g)>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="adm-label">Care instructions</label>
                <textarea name="care_instructions" rows="2" class="adm-input">{{ old('care_instructions', $product?->care_instructions) }}</textarea>
            </div>
        </div>

        <div class="adm-card p-6 space-y-4">
            <h2 class="adm-card-title">Pricing & stock</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="adm-label">Price *</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product?->price) }}" class="adm-input" required>
                    @error('price')<p class="adm-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="adm-label">Compare price</label>
                    <input type="number" step="0.01" min="0" name="compare_price" value="{{ old('compare_price', $product?->compare_price) }}" class="adm-input">
                </div>
                <div>
                    <label class="adm-label">Cost price</label>
                    <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $product?->cost_price) }}" class="adm-input">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="adm-label">Stock</label>
                    <input type="number" min="0" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" class="adm-input">
                </div>
                <div class="flex items-end pb-2">
                    <label class="adm-checkbox"><input type="checkbox" name="track_stock" value="1" @checked(old('track_stock', $product?->track_stock ?? true))> Track stock</label>
                </div>
            </div>
        </div>

        <div class="adm-card p-6 space-y-4" x-data="{ hasVariants: {{ old('has_variants', $product?->has_variants) ? 'true' : 'false' }}, variants: {{ Js::from($variants) }} }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="adm-card-title">Variants</h2>
                <label class="adm-checkbox"><input type="checkbox" name="has_variants" value="1" x-model="hasVariants"> Has variants</label>
            </div>
            <div x-show="hasVariants" x-cloak class="space-y-3">
                <template x-for="(row, i) in variants" :key="i">
                    <div class="grid gap-2 sm:grid-cols-7 items-end border-b border-ink/5 pb-3">
                        <input type="hidden" :name="'variants['+i+'][id]'" :value="row.id || ''">
                        <div><label class="adm-label">Size</label><input class="adm-input" :name="'variants['+i+'][size]'" x-model="row.size"></div>
                        <div><label class="adm-label">Color</label><input class="adm-input" :name="'variants['+i+'][color]'" x-model="row.color"></div>
                        <div><label class="adm-label">Code</label><input class="adm-input" :name="'variants['+i+'][color_code]'" x-model="row.color_code" placeholder="#hex"></div>
                        <div><label class="adm-label">SKU</label><input class="adm-input" :name="'variants['+i+'][sku]'" x-model="row.sku"></div>
                        <div><label class="adm-label">Stock</label><input type="number" min="0" class="adm-input" :name="'variants['+i+'][stock]'" x-model="row.stock"></div>
                        <div class="flex gap-1 items-end">
                            <div class="flex-1"><label class="adm-label">Price</label><input type="number" step="0.01" class="adm-input" :name="'variants['+i+'][price]'" x-model="row.price"></div>
                            <button type="button" class="btn btn-ghost btn-sm text-rose-600" @click="variants.splice(i,1)"><x-icon name="trash" class="h-4 w-4" /></button>
                        </div>
                    </div>
                </template>
                <button type="button" class="btn btn-secondary btn-sm" @click="variants.push({id:'',size:'',color:'',color_code:'',sku:'',stock:0,price:''})">
                    <x-icon name="plus" class="h-4 w-4" /> Add variant
                </button>
            </div>
        </div>

        <div class="adm-card p-6 space-y-4">
            <h2 class="adm-card-title">Images</h2>
            @if($product && $product->images->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach($product->images as $image)
                        <div class="relative rounded-xl border border-ink/10 p-2">
                            <img src="{{ img($image->path) }}" alt="" class="aspect-[3/4] w-full rounded-lg object-cover bg-paper-100">
                            @if($image->is_primary)<span class="pill mt-2 bg-emerald-100 text-emerald-800">Primary</span>@endif
                            <div class="mt-2 flex flex-wrap gap-1">
                                @unless($image->is_primary)
                                    <form method="POST" action="{{ route('admin.products.images.primary', [$product, $image]) }}">
                                        @csrf
                                        <button class="btn btn-ghost btn-sm">Primary</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" onsubmit="return confirm('Delete image?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div>
                <label class="adm-label">Upload images</label>
                <input type="file" name="images[]" accept="image/*" multiple class="adm-input">
                <p class="adm-hint">Max 5MB each</p>
                @error('images.*')<p class="adm-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="adm-card p-6 space-y-4">
            <h2 class="adm-card-title">SEO</h2>
            <div>
                <label class="adm-label">Meta title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $product?->meta_title) }}" class="adm-input">
            </div>
            <div>
                <label class="adm-label">Meta description</label>
                <textarea name="meta_description" rows="2" class="adm-input">{{ old('meta_description', $product?->meta_description) }}</textarea>
            </div>
            <div>
                <label class="adm-label">Tags</label>
                <input type="text" name="tags" value="{{ $tagsValue }}" class="adm-input" placeholder="comma, separated">
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="adm-card p-6 space-y-3">
            <h2 class="adm-card-title">Status</h2>
            <label class="adm-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true))> Active</label>
            <label class="adm-checkbox"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product?->is_featured))> Featured</label>
            <label class="adm-checkbox"><input type="checkbox" name="is_new" value="1" @checked(old('is_new', $product?->is_new))> New</label>
            <label class="adm-checkbox"><input type="checkbox" name="is_trending" value="1" @checked(old('is_trending', $product?->is_trending))> Trending</label>
        </div>
        <div class="adm-card p-6 space-y-4">
            <h2 class="adm-card-title">Badge</h2>
            <div>
                <label class="adm-label">Badge text</label>
                <input type="text" name="badge" value="{{ old('badge', $product?->badge) }}" class="adm-input">
            </div>
            <div>
                <label class="adm-label">Badge color</label>
                <input type="text" name="badge_color" value="{{ old('badge_color', $product?->badge_color) }}" class="adm-input" placeholder="#hex">
            </div>
        </div>
        <div class="adm-card p-6 space-y-3">
            <h2 class="adm-card-title">Collections</h2>
            <div class="max-h-64 space-y-2 overflow-y-auto">
                @forelse($collections as $col)
                    <label class="adm-checkbox">
                        <input type="checkbox" name="collection_ids[]" value="{{ $col->id }}" @checked(in_array($col->id, (array) $selectedCollections))>
                        {{ $col->name }}
                    </label>
                @empty
                    <p class="text-sm text-ink-300">No collections</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
