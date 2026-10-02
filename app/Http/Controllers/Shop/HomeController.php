<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetLocale;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Collection;
use App\Models\FlashSale;
use App\Models\Post;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $with = ['primaryImage', 'images', 'category'];

        return view('shop.home', [
            'heroBanners' => Banner::active()->position('hero')->get(),
            'promoBanner' => Banner::active()->position('promo')->first(),
            'featuredCategories' => Category::active()->featured()->root()
                ->withCount(['products' => fn ($q) => $q->active()])
                ->with(['children' => fn ($q) => $q->withCount(['products' => fn ($p) => $p->active()])])
                ->orderBy('sort_order')->get(),
            'featured' => Product::active()->featured()->with($with)->latest('published_at')->take(8)->get(),
            'newArrivals' => Product::active()->newArrivals()->with($with)->take(8)->get(),
            'trending' => Product::active()->trending()->with($with)->orderByDesc('sold_count')->take(8)->get(),
            'flashSale' => setting('show_flash_sale', '1') ? FlashSale::running()->with('items.product.primaryImage')->first() : null,
            'collections' => Collection::active()->orderBy('sort_order')->take(3)->get(),
            'testimonials' => setting('show_testimonials', '1') ? Testimonial::active()->get() : collect(),
            'posts' => setting('show_blog', '1') ? Post::published()->with('category')->take(3)->get() : collect(),
        ]);
    }

    public function locale(string $locale): RedirectResponse
    {
        if (in_array($locale, SetLocale::SUPPORTED)) {
            session(['locale' => $locale]);
        }

        return back();
    }
}
