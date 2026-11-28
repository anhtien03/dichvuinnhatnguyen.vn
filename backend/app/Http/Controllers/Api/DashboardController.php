<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\ErpCustomer;
use App\Models\ErpOrder;
use App\Models\ErpOrderItem;
use App\Models\Post;
use App\Models\Product;
use App\Models\QuoteRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->is_active, 403);

        $totalQuotes = $user->hasPermission('quotes.view') ? QuoteRequest::count() : 0;
        $pendingQuotes = $user->hasPermission('quotes.view') ? QuoteRequest::where('status', 'pending')->count() : 0;
        $contactedQuotes = $user->hasPermission('quotes.view') ? QuoteRequest::where('status', 'contacted')->count() : 0;
        $approvedQuotes = $user->hasPermission('quotes.view') ? QuoteRequest::where('status', 'approved')->count() : 0;
        $completedQuotes = $user->hasPermission('quotes.view') ? QuoteRequest::where('status', 'completed')->count() : 0;

        $totalProducts = $user->hasPermission('products.manage') ? Product::count() : 0;
        $activeProducts = $user->hasPermission('products.manage') ? Product::where('is_active', true)->count() : 0;
        $totalCategories = $user->hasPermission('categories.manage') ? Category::count() : 0;

        $totalPosts = $user->hasPermission('posts.manage') ? Post::count() : 0;
        $publishedPosts = $user->hasPermission('posts.manage') ? Post::where('is_published', true)->count() : 0;
        $totalBanners = $user->hasPermission('banners.manage') ? Banner::count() : 0;
        $activeBanners = $user->hasPermission('banners.manage') ? Banner::where('is_active', true)->count() : 0;

        $recentQuotes = $user->hasPermission('quotes.view') ? QuoteRequest::with('product')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get() : collect();

        $recentPosts = $user->hasPermission('posts.manage') ? Post::orderBy('id', 'desc')
            ->take(5)
            ->get() : collect();

        // 1. Chart Series (Đơn hàng mới + Khách hàng mới) for day / week / month
        $now = Carbon::now();
        $sixMonthsAgo = $now->copy()->startOfMonth()->subMonths(5);

        $orderDates = $user->hasPermission('orders.view') ? ErpOrder::where('created_at', '>=', $sixMonthsAgo)->pluck('created_at') : collect();
        $customerDates = $user->hasPermission('clients.view') ? ErpCustomer::where('created_at', '>=', $sixMonthsAgo)->pluck('created_at') : collect();

        // Daily (last 7 days)
        $daySeries = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i);
            $dStr = $d->format('Y-m-d');
            $daySeries[] = [
                'label' => $i === 0 ? 'Hôm nay' : $d->format('d/m'),
                'orders' => $orderDates->filter(fn($dt) => Carbon::parse($dt)->format('Y-m-d') === $dStr)->count(),
                'customers' => $customerDates->filter(fn($dt) => Carbon::parse($dt)->format('Y-m-d') === $dStr)->count(),
            ];
        }

        // Weekly (last 6 weeks)
        $weekSeries = [];
        for ($i = 5; $i >= 0; $i--) {
            $wStart = $now->copy()->startOfWeek()->subWeeks($i);
            $wEnd = $wStart->copy()->endOfWeek();
            $weekSeries[] = [
                'label' => $i === 0 ? 'Tuần này' : 'T.' . $wStart->format('d/m'),
                'orders' => $orderDates->filter(function ($dt) use ($wStart, $wEnd) {
                    $c = Carbon::parse($dt);
                    return $c->between($wStart, $wEnd);
                })->count(),
                'customers' => $customerDates->filter(function ($dt) use ($wStart, $wEnd) {
                    $c = Carbon::parse($dt);
                    return $c->between($wStart, $wEnd);
                })->count(),
            ];
        }

        // Monthly (last 6 months)
        $monthSeries = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->startOfMonth()->subMonths($i);
            $mStr = $m->format('Y-m');
            $monthSeries[] = [
                'label' => 'T' . $m->format('m/y'),
                'orders' => $orderDates->filter(fn($dt) => Carbon::parse($dt)->format('Y-m') === $mStr)->count(),
                'customers' => $customerDates->filter(fn($dt) => Carbon::parse($dt)->format('Y-m') === $mStr)->count(),
            ];
        }

        // 2. Top Printed Products from ERP Order Items
        $totalOrderItems = $user->hasPermission('orders.view') ? (int) ErpOrderItem::whereHas('order')->count() : 0;
        $topProductsRaw = $user->hasPermission('orders.view') ? ErpOrderItem::whereHas('order')
            ->select(
                'product_name',
                DB::raw('COUNT(DISTINCT order_id) as orders_count'),
                DB::raw('COUNT(*) as items_count'),
                DB::raw('SUM(quantity) as total_qty')
            )
            ->groupBy('product_name')
            ->orderByDesc('orders_count')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get() : collect();

        $topProducts = $topProductsRaw->map(function ($row) use ($totalOrderItems) {
            $pct = $totalOrderItems > 0 ? round(($row->items_count / $totalOrderItems) * 100) : 0;
            return [
                'name' => $row->product_name ?: 'Sản phẩm khác',
                'orders_count' => (int) $row->orders_count,
                'items_count' => (int) $row->items_count,
                'total_qty' => (float) $row->total_qty,
                'percent' => (int) $pct,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'quotes' => [
                    'total' => $totalQuotes,
                    'pending' => $pendingQuotes,
                    'contacted' => $contactedQuotes,
                    'approved' => $approvedQuotes,
                    'completed' => $completedQuotes,
                ],
                'products' => [
                    'total' => $totalProducts,
                    'active' => $activeProducts,
                ],
                'categories' => [
                    'total' => $totalCategories,
                ],
                'posts' => [
                    'total' => $totalPosts,
                    'published' => $publishedPosts,
                ],
                'banners' => [
                    'total' => $totalBanners,
                    'active' => $activeBanners,
                ],
                'recent_quotes' => $recentQuotes,
                'recent_posts' => $recentPosts,
                'chart_series' => [
                    'day' => $daySeries,
                    'week' => $weekSeries,
                    'month' => $monthSeries,
                ],
                'top_products' => $topProducts,
                'total_order_items' => $totalOrderItems,
            ],
        ]);
    }
}
