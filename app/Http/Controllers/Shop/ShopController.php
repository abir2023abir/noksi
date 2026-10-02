<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        return $this->render($request, Product::query(), [
            'title' => __('All Products'),
            'subtitle' => __('Everything we make, in one place.'),
        ]);
    }

    public function category(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);
        $ids = $category->allIds();

        return $this->render($request, Product::whereIn('category_id', $ids), [
            'title' => $category->name,
            'subtitle' => $category->description,
            'category' => $category,
            'banner' => $category->banner ?: $category->image,
            'color' => $category->color,
        ]);
    }

    public function brand(Request $request, Brand $brand)
    {
        return $this->render($request, Product::where('brand_id', $brand->id), [
            'title' => $brand->name,
            'subtitle' => $brand->description,
        ]);
    }

    public function collection(Request $request, Collection $collection)
    {
        abort_unless($collection->is_active, 404);

        return $this->render($request, Product::whereHas('collections', fn ($q) => $q->where('collections.id', $collection->id)), [
            'title' => $collection->name,
            'subtitle' => $collection->subtitle ?: $collection->description,
            'banner' => $collection->image,
            'color' => $collection->color,
        ]);
    }

    public function newArrivals(Request $request)
    {
        return $this->render($request, Product::where('is_new', true), [
            'title' => __('New Arrivals'),
            'subtitle' => __('Fresh off the loom and the cutting table.'),
        ]);
    }

    public function sale(Request $request)
    {
        return $this->render($request, Product::whereNotNull('compare_price')->whereColumn('compare_price', '>', 'price'), [
            'title' => __('Sale'),
            'subtitle' => __('Good things, gently reduced.'),
            'color' => '#C2410C',
        ]);
    }

    public function flashSale(Request $request)
    {
        $sale = FlashSale::running()->first();
        if (! $sale) {
            return redirect()->route('shop.sale');
        }

        return $this->render($request, Product::whereHas('flashSaleProducts', fn ($q) => $q->where('flash_sale_id', $sale->id)), [
            'title' => $sale->title,
            'subtitle' => $sale->subtitle,
            'color' => $sale->color,
            'flashSale' => $sale,
        ]);
    }

    public function search(Request $request)
    {
        $term = trim((string) $request->get('q'));

        return $this->render($request, Product::search($term), [
            'title' => $term ? __('Results for “:term”', ['term' => $term]) : __('Search'),
            'subtitle' => null,
            'term' => $term,
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $term = trim((string) $request->get('q'));
        if (mb_strlen($term) < 2) {
            return response()->json(['products' => [], 'categories' => []]);
        }

        $products = Product::active()->search($term)->with('primaryImage', 'category')->take(6)->get()
            ->map(fn (Product $p) => [
                'name' => $p->name,
                'url' => $p->url,
                'image' => $p->image_url,
                'price' => money($p->current_price),
                'category' => $p->category?->name,
            ]);

        $categories = Category::active()->where('name', 'like', "%{$term}%")->take(4)->get()
            ->map(fn (Category $c) => ['name' => $c->name, 'url' => $c->url]);

        return response()->json(compact('products', 'categories'));
    }

    /* ------------------------------------------------------------------ */

    protected function render(Request $request, Builder $query, array $meta)
    {
        $query->active()->with(['primaryImage', 'images', 'category']);

        // Filters
        if ($cats = $request->get('categories')) {
            $ids = collect((array) $cats)->flatMap(fn ($slug) => Category::where('slug', $slug)->first()?->allIds() ?? [])->all();
            $query->whereIn('category_id', $ids);
        }
        if ($brands = $request->get('brands')) {
            $query->whereHas('brand', fn ($q) => $q->whereIn('slug', (array) $brands));
        }
        if ($sizes = $request->get('sizes')) {
            $query->whereHas('variants', fn ($q) => $q->whereIn('size', (array) $sizes)->where('is_active', true));
        }
        if ($colors = $request->get('colors')) {
            $query->whereHas('variants', fn ($q) => $q->whereIn('color', (array) $colors)->where('is_active', true));
        }
        if ($fabrics = $request->get('fabrics')) {
            $query->whereHas('attributeValues', fn ($q) => $q->whereIn('slug', (array) $fabrics)->whereHas('attribute', fn ($a) => $a->where('slug', 'fabric')));
        }
        if ($gender = $request->get('gender')) {
            $query->whereIn('gender', (array) $gender);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('compare_price')->whereColumn('compare_price', '>', 'price');
        }
        if ($request->filled('rating')) {
            $query->where('rating_avg', '>=', (int) $request->rating);
        }

        // Sorting
        match ($request->get('sort', 'featured')) {
            'newest' => $query->latest('published_at'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('sold_count'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('rating_count'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('is_featured')->orderByDesc('is_trending')->latest('published_at'),
        };

        $perPage = (int) setting('products_per_page', 12);
        $products = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('shop.partials.product-grid', compact('products'))->render();
        }

        $filterData = [
            'categories' => Category::active()->root()->with('children')->orderBy('sort_order')->get(),
            'brands' => Brand::active()->orderBy('name')->get(),
            'sizes' => Attribute::where('slug', 'size')->first()?->values ?? collect(),
            'colors' => Attribute::where('slug', 'color')->first()?->values ?? collect(),
            'fabrics' => Attribute::where('slug', 'fabric')->first()?->values ?? collect(),
            'priceMax' => (int) ceil((Product::active()->max('price') ?: 10000) / 500) * 500,
        ];

        return view('shop.index', array_merge($meta, ['products' => $products, 'filters' => $filterData]));
    }
}
