<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Services\CartService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);

        $categoriesCache = null;
        $cartCache = null;

        $shareData = function (ViewContract $view) use (&$categoriesCache, &$cartCache): void {
            if ($categoriesCache === null) {
                $categoriesCache = Cache::remember('store_categories', 300, fn () => Category::query()
                    ->active()
                    ->whereNull('parent_id')
                    ->with(['children' => fn ($q) => $q->active()->orderBy('sort_order')])
                    ->orderBy('sort_order')
                    ->get(['id', 'name', 'slug', 'parent_id', 'image']));
            }

            if ($cartCache === null) {
                $cartCache = app(CartService::class)->summary();
            }

            $view->with('storeCategories', $categoriesCache);
            $view->with('storeCartSummary', $cartCache);
        };

        View::composer(['store.*', 'layouts.store'], $shareData);
    }
}
