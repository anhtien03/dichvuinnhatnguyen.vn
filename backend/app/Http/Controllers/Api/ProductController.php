<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\GoogleIndexingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->orderBy('sort_order', 'asc')->orderBy('id', 'desc');

        if ($request->has('category') && !empty($request->category)) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->boolean('popular')) {
            $query->where('is_popular', true);
        }

        if (!$request->user() && !$request->boolean('all')) {
            $query->where('is_active', true);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show($slugOrId)
    {
        $product = Product::with('category')
            ->where('slug', $slugOrId)
            ->orWhere('id', $slugOrId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'nullable|exists:categories,id',
            'tag' => 'nullable|string|max:100',
            'base_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'turnaround' => 'nullable|string|max:100',
            'min_quantity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'specs' => 'nullable|array',
            'features' => 'nullable|array',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:500',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Ensure uniqueness
            $count = Product::where('slug', 'like', $validated['slug'] . '%')->count();
            if ($count > 0) {
                $validated['slug'] .= '-' . ($count + 1);
            }
        }

        $product = Product::create($validated);

        if (SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $indexingService->publishUrl('https://innhatnguyen.vn/san-pham/' . $product->slug, 'URL_UPDATED');
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Thêm ấn phẩm thành công',
            'data' => $product->load('category'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'slug' => 'sometimes|required|string|unique:products,slug,' . $product->id,
            'tag' => 'nullable|string|max:100',
            'base_price' => 'sometimes|required|numeric|min:0',
            'unit' => 'sometimes|required|string|max:50',
            'turnaround' => 'nullable|string|max:100',
            'min_quantity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'specs' => 'nullable|array',
            'features' => 'nullable|array',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|string|max:500',
        ]);

        $product->update($validated);

        if (SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $indexingService->publishUrl('https://innhatnguyen.vn/san-pham/' . $product->slug, 'URL_UPDATED');
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ấn phẩm thành công',
            'data' => $product->load('category'),
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $deletedSlug = $product->slug;
        $product->delete();

        if (SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $indexingService->publishUrl('https://innhatnguyen.vn/san-pham/' . $deletedSlug, 'URL_DELETED');
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ấn phẩm',
        ]);
    }
}

