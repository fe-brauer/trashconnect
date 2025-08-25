<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\StaticPage;
use Livewire\Livewire;

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
        View::composer('components.layouts.app', function ($view) {
            $pages = Cache::remember('main_nav_pages', 600, function () {
                return StaticPage::query()
                    ->where('published', true)
                    ->where('show_in_nav', true)
                    ->orderByRaw('COALESCE(nav_order, 999999)')
                    ->orderBy('title')
                    ->get(['title','slug']);
            });
            $view->with('navPages', $pages);
        });
    }
}
