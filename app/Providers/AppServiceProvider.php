<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Page;
use App\Services\CartService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(CartService::class);
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.nokshi');
        Paginator::defaultSimpleView('vendor.pagination.simple-nokshi');

        // Storefront layout data: menu tree, footer pages, cart summary.
        View::composer(['components.layouts.app', 'partials.header', 'partials.footer', 'partials.mobile-menu'], function ($view) {
            if (! Schema::hasTable('categories')) {
                return;
            }

            $menu = Cache::remember('menu.categories', 3600, fn () => Category::active()->root()
                ->where('show_in_menu', true)
                ->with(['children' => fn ($q) => $q->active()->where('show_in_menu', true)->with('children')])
                ->orderBy('sort_order')->get());

            $footerPages = Cache::remember('footer.pages', 3600, fn () => Page::active()->where('show_in_footer', true)->get(['title', 'slug']));

            $view->with('menuCategories', $menu)->with('footerPages', $footerPages);
        });

        Blade::if('staff', fn () => auth()->check() && auth()->user()->isStaff());

        Blade::directive('money', fn ($expr) => "<?php echo money($expr); ?>");
    }
}
