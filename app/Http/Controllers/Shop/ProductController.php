<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->is_active || auth()->user()?->isStaff(), 404);

        $product->load(['images', 'variants' => fn ($q) => $q->where('is_active', true), 'category.parent', 'brand', 'attributeValues.attribute']);
        $product->increment('views');

        $related = $product->related()->active()->with('primaryImage', 'images', 'category')->take(4)->get();
        if ($related->count() < 4) {
            $more = Product::active()->where('category_id', $product->category_id)->where('id', '!=', $product->id)
                ->whereNotIn('id', $related->pluck('id'))->with('primaryImage', 'images', 'category')->inRandomOrder()->take(4 - $related->count())->get();
            $related = $related->concat($more);
        }

        $reviews = $product->approvedReviews()->with('user')->paginate(5, ['*'], 'reviews');
        $ratingBreakdown = collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($r) => [$r => $product->approvedReviews()->where('rating', $r)->count()]);
        $canReview = auth()->check() && ! $product->reviews()->where('user_id', auth()->id())->exists();
        $flashSale = $product->activeFlashSale();

        $recent = collect(session('recently_viewed', []))->reject(fn ($id) => $id === $product->id)->prepend($product->id)->take(8)->values();
        session(['recently_viewed' => $recent->all()]);
        $recentlyViewed = Product::active()->whereIn('id', $recent->slice(1))->with('primaryImage', 'images', 'category')->get();

        return view('shop.product', compact('product', 'related', 'reviews', 'ratingBreakdown', 'canReview', 'flashSale', 'recentlyViewed'));
    }

    public function quickView(Product $product): string
    {
        $product->load(['images', 'variants' => fn ($q) => $q->where('is_active', true)]);

        return view('shop.partials.quick-view', ['product' => $product, 'flashSale' => $product->activeFlashSale()])->render();
    }

    public function review(Request $request, Product $product, ImageService $images): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:2000'],
            'name' => [auth()->check() ? 'nullable' : 'required', 'string', 'max:100'],
            'images.*' => ['nullable', 'image', 'max:4096'],
        ]);

        if (auth()->check() && $product->reviews()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', __('You have already reviewed this product.'));
        }

        $paths = [];
        foreach ($request->file('images', []) as $file) {
            $paths[] = $images->store($file, 'reviews', 1200);
        }

        $verified = auth()->check() && $product->orderItems()
            ->whereHas('order', fn ($q) => $q->where('user_id', auth()->id())->where('status', 'delivered'))->exists();

        Review::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'name' => $data['name'] ?? null,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
            'images' => $paths ?: null,
            'is_verified' => $verified,
            'status' => setting('reviews_auto_approve', '0') === '1' ? 'approved' : 'pending',
        ]);

        return back()->with('success', __('Thanks! Your review has been submitted and will appear once approved.'));
    }
}
