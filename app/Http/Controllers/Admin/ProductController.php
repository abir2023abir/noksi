<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'brand', 'primaryImage'])
            ->when($request->q, fn ($q, $term) => $q->search($term))
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->brand_id, fn ($q, $id) => $q->where('brand_id', $id))
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($request->stock === 'low', fn ($q) => $q->where('track_stock', true)->where('stock', '<=', 5)->where('stock', '>', 0))
            ->when($request->stock === 'out', fn ($q) => $q->where('track_stock', true)->where('stock', '<=', 0))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands' => Brand::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('admin.products.create', $this->formData());
    }

    public function store(Request $request, ImageService $images)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_new'] = $request->boolean('is_new');
        $data['is_trending'] = $request->boolean('is_trending');
        $data['track_stock'] = $request->boolean('track_stock', true);
        $data['has_variants'] = $request->boolean('has_variants');
        $data['tags'] = $this->parseTags($request->input('tags'));

        $product = Product::create($data);

        if ($request->filled('collection_ids')) {
            $product->collections()->sync($request->input('collection_ids', []));
        }

        if ($request->hasFile('images')) {
            $this->storeUploadedImages($product, $request->file('images'), $images);
        }

        if ($request->boolean('has_variants') && $request->filled('variants')) {
            $this->persistVariants($product, $request->input('variants', []));
        }

        return redirect()->route('admin.products.edit', $product)->with('success', __('Product created.'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'variants', 'collections', 'reviews.user']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants', 'collections']);

        return view('admin.products.edit', array_merge($this->formData(), compact('product')));
    }

    public function update(Request $request, Product $product, ImageService $images)
    {
        $data = $this->validated($request, $product);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_new'] = $request->boolean('is_new');
        $data['is_trending'] = $request->boolean('is_trending');
        $data['track_stock'] = $request->boolean('track_stock');
        $data['has_variants'] = $request->boolean('has_variants');
        $data['tags'] = $this->parseTags($request->input('tags'));

        $product->update($data);
        $product->collections()->sync($request->input('collection_ids', []));

        if ($request->hasFile('images')) {
            $this->storeUploadedImages($product, $request->file('images'), $images);
        }

        if ($request->boolean('has_variants') && $request->has('variants')) {
            $this->persistVariants($product, $request->input('variants', []));
        }

        return back()->with('success', __('Product updated.'));
    }

    public function destroy(Product $product, ImageService $images)
    {
        foreach ($product->images as $image) {
            $images->delete($image->path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', __('Product deleted.'));
    }

    public function uploadImages(Request $request, Product $product, ImageService $images)
    {
        $request->validate(['images' => ['required', 'array'], 'images.*' => ['image', 'max:5120']]);
        $this->storeUploadedImages($product, $request->file('images'), $images);

        return back()->with('success', __('Images uploaded.'));
    }

    public function deleteImage(Product $product, ProductImage $image, ImageService $images)
    {
        abort_unless($image->product_id === $product->id, 404);
        $wasPrimary = $image->is_primary;
        $images->delete($image->path);
        $image->delete();

        if ($wasPrimary && $next = $product->images()->first()) {
            $next->update(['is_primary' => true]);
        }

        return back()->with('success', __('Image deleted.'));
    }

    public function primaryImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);
        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', __('Primary image updated.'));
    }

    public function reorderImages(Request $request, Product $product)
    {
        $data = $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);
        foreach ($data['order'] as $sort => $id) {
            ProductImage::where('product_id', $product->id)->where('id', $id)->update(['sort_order' => $sort]);
        }

        return response()->json(['ok' => true]);
    }

    public function saveVariants(Request $request, Product $product)
    {
        $request->validate([
            'variants' => ['required', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.size' => ['nullable', 'string', 'max:20'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.color_code' => ['nullable', 'string', 'max:20'],
            'variants.*.sku' => ['nullable', 'string', 'max:50'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $product->update(['has_variants' => true]);
        $this->persistVariants($product, $request->input('variants', []));

        return back()->with('success', __('Variants saved.'));
    }

    public function duplicate(Product $product)
    {
        $copy = $product->replicate(['slug', 'sku', 'sold_count', 'views', 'rating_avg', 'rating_count']);
        $copy->name = $product->name.' (Copy)';
        $copy->slug = null;
        $copy->sku = null;
        $copy->is_active = false;
        $copy->save();

        foreach ($product->images as $image) {
            $copy->images()->create($image->only(['path', 'alt', 'color_slug', 'is_primary', 'sort_order']));
        }
        foreach ($product->variants as $variant) {
            $v = $variant->replicate(['sku']);
            $v->sku = null;
            $copy->variants()->save($v);
        }
        $copy->collections()->sync($product->collections->pluck('id'));

        return redirect()->route('admin.products.edit', $copy)->with('success', __('Product duplicated.'));
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:products,id'],
            'action' => ['required', 'in:activate,deactivate,feature,unfeature,delete'],
        ]);

        $query = Product::whereIn('id', $data['ids']);

        match ($data['action']) {
            'activate' => $query->update(['is_active' => true]),
            'deactivate' => $query->update(['is_active' => false]),
            'feature' => $query->update(['is_featured' => true]),
            'unfeature' => $query->update(['is_featured' => false]),
            'delete' => $query->delete(),
        };

        return back()->with('success', __('Bulk action completed.'));
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', __('Product status updated.'));
    }

    protected function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product?->id)],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product?->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'material' => ['nullable', 'string', 'max:255'],
            'care_instructions' => ['nullable', 'string'],
            'gender' => ['nullable', 'in:men,women,unisex,kids'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            'has_variants' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_new' => ['nullable', 'boolean'],
            'is_trending' => ['nullable', 'boolean'],
            'badge' => ['nullable', 'string', 'max:50'],
            'badge_color' => ['nullable', 'string', 'max:20'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'tags' => ['nullable'],
            'collection_ids' => ['nullable', 'array'],
            'collection_ids.*' => ['integer', 'exists:collections,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:5120'],
            'variants' => ['nullable', 'array'],
        ]);
    }

    protected function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'collections' => Collection::orderBy('name')->get(),
        ];
    }

    protected function parseTags(mixed $tags): ?array
    {
        if (is_array($tags)) {
            return array_values(array_filter(array_map('trim', $tags)));
        }
        if (is_string($tags) && $tags !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $tags))));
        }

        return null;
    }

    protected function storeUploadedImages(Product $product, array $files, ImageService $images): void
    {
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $sort = (int) $product->images()->max('sort_order');

        foreach ($files as $i => $file) {
            $path = $images->store($file, 'products', 1600);
            $product->images()->create([
                'path' => $path,
                'is_primary' => ! $hasPrimary && $i === 0,
                'sort_order' => ++$sort,
            ]);
        }
    }

    protected function persistVariants(Product $product, array $variants): void
    {
        $kept = [];

        foreach ($variants as $row) {
            if (blank($row['size'] ?? null) && blank($row['color'] ?? null) && blank($row['sku'] ?? null)) {
                continue;
            }

            $payload = [
                'size' => $row['size'] ?? null,
                'color' => $row['color'] ?? null,
                'color_code' => $row['color_code'] ?? null,
                'sku' => $row['sku'] ?? null,
                'stock' => (int) ($row['stock'] ?? 0),
                'price' => $row['price'] ?? null,
                'is_active' => array_key_exists('is_active', $row) ? (bool) $row['is_active'] : true,
            ];

            if (! empty($row['id'])) {
                $variant = ProductVariant::where('product_id', $product->id)->where('id', $row['id'])->first();
                if ($variant) {
                    $variant->update($payload);
                    $kept[] = $variant->id;
                    continue;
                }
            }

            $variant = $product->variants()->create($payload);
            $kept[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $kept ?: [0])->delete();
        $product->syncStockFromVariants();
    }
}
