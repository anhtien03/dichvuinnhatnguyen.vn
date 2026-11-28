<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\PublicSeoSnapshot;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            RateLimiter::for($name, static function (Request $request) use ($perMinute, $name) {
                return Limit::perMinute($perMinute)->by($request->ip())
                    ->response(static function (Request $request, array $headers) use ($name) {
                        try {
                            // Global sampling per limiter bounds log volume even under distributed abuse.
                            if (Cache::add('security:rate-limit-log:' . $name, true, 60)) {
                                Log::channel('security')->warning('api_rate_limit_exceeded', [
                                    'limiter' => $name,
                                    'ip_fingerprint' => hash_hmac('sha256', (string) $request->ip(), (string) config('app.key')),
                                    'method' => $request->method(),
                                ]);
                            }
                        } catch (\Throwable $e) {
                            // Logging failures must not change the rate-limit response.
                        }

                        return response()->json([
                            'message' => 'Too Many Attempts.',
                        ], 429, $headers);
                    });
            });
        }

        foreach ([Product::class, Category::class, Post::class, Banner::class, SiteSetting::class] as $modelClass) {
            $modelClass::saved(static function ($model): void {
                if (!config('seo.snapshots')) {
                    return;
                }
                DB::afterCommit(static function () use ($model): void {
                    try {
                        PublicSeoSnapshot::invalidateForModel($model, 'saved');
                    } catch (\Throwable $e) {
                        Log::warning('seo_snapshot_invalidation_failed', ['error' => $e->getMessage()]);
                    }
                });
            });

            $modelClass::deleted(static function ($model): void {
                if (!config('seo.snapshots')) {
                    return;
                }
                DB::afterCommit(static function () use ($model): void {
                    try {
                        PublicSeoSnapshot::invalidateForModel($model, 'deleted');
                    } catch (\Throwable $e) {
                        Log::warning('seo_snapshot_invalidation_failed', ['error' => $e->getMessage()]);
                    }
                });
            });
        }
    }
}
