<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();

        $totalQuotes = QuoteRequest::count();
        $pendingQuotes = QuoteRequest::where('status', 'pending')->count();
        $contactedQuotes = QuoteRequest::where('status', 'contacted')->count();
        $approvedQuotes = QuoteRequest::where('status', 'approved')->count();
        $completedQuotes = QuoteRequest::where('status', 'completed')->count();

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::count();

        $totalPosts = Post::count();
        $publishedPosts = Post::where('is_published', true)->count();
        $totalBanners = Banner::count();
        $activeBanners = Banner::where('is_active', true)->count();

        $recentQuotes = QuoteRequest::with('product')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $recentPosts = Post::orderBy('id', 'desc')
            ->take(5)
            ->get();

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
            ],
        ]);
    }
}
