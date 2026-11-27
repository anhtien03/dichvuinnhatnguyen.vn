<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        // Separate budgets prevent catalog browsing from exhausting quote/login limits.
        foreach ([
            'catalog-list' => 120,
            'catalog-detail' => 180,
            'catalog-categories' => 120,
            'catalog-display' => 120,
            'catalog-news' => 120,
            'public-login' => 10,
            'public-quotes' => 10,
        ] as $name => $perMinute) {
            RateLimiter::for($name, static function (Request $request) use ($perMinute) {
                return Limit::perMinute($perMinute)->by($request->ip());
            });
        }
    }
}
