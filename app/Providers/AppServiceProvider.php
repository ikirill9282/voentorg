<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
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
        View::composer('store.*', function (ViewContract $view): void {
            $view->with('storeCategories', Category::query()
                ->active()
                ->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->active()->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'parent_id', 'image']));

            $view->with('storeCartSummary', app(CartService::class)->summary());
        });

        View::composer('layouts.store', function (ViewContract $view): void {
            $view->with('storeCategories', Category::query()
                ->active()
                ->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->active()->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'parent_id', 'image']));

            $view->with('storeCartSummary', app(CartService::class)->summary());
        });
    }
}
