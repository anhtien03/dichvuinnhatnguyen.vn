<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Services\GoogleIndexingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private function canReadHiddenProducts(): bool
    {
        $user = Auth::guard('sanctum')->user();
        return $user !== null && $user->hasPermission('products.manage');
    }

    public function index(Request $request)
    {
        $query = Product::with('category.parent')->orderBy('sort_order', 'asc')->orderBy('id', 'desc');

        if ($request->has('category') && !empty($request->category)) {
            $catSlug = $request->category;
            $query->whereHas('category', function ($q) use ($catSlug) {
                $q->where('slug', $catSlug)
                  ->orWhereHas('parent', function ($pq) use ($catSlug) {
                      $pq->where('slug', $catSlug);
                  });
            });
        }

        if ($request->boolean('popular')) {
            $query->where('is_popular', true);
        }

        if (!$request->boolean('all') || !$this->canReadHiddenProducts()) {
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

        if ($request->boolean('compact')) {
            $products->transform(function ($p) {
                $plain = trim(preg_replace('/\s+/', ' ', strip_tags($p->description ?? '')));
                $p->description = Str::limit($plain, 180, '...');
                unset($p->specs, $p->gallery, $p->content);
                return $p;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show(Request $request, $slugOrId)
    {
        $query = Product::with('category.parent')->where(function ($q) use ($slugOrId) {
            $q->where('slug', $slugOrId)->orWhere('id', $slugOrId);
        });
        if (!$request->boolean('all') || !$this->canReadHiddenProducts()) {
            $query->where('is_active', true);
        }
        $product = $query->firstOrFail();

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

        if (!$request->user()->hasPermission('pricing.manage')) {
            $validated['base_price'] = 0;
            $validated['specs'] = ['hide_pricing' => true, 'custom_specs' => $validated['specs']['custom_specs'] ?? []];
        }

        $product = Product::create($validated);

        if (SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $indexingService->publishUrl('https://dichvuinnhatnguyen.vn/san-pham/' . $product->slug, 'URL_UPDATED');
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

        if (!$request->user()->hasPermission('pricing.manage')) {
            abort_if(array_key_exists('base_price', $validated) || array_key_exists('specs', $validated), 403, 'Pricing permission required.');
        }

        $product->update($validated);

        if (SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $indexingService->publishUrl('https://dichvuinnhatnguyen.vn/san-pham/' . $product->slug, 'URL_UPDATED');
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ấn phẩm thành công',
            'data' => $product->load('category'),
        ]);
    }

    public function updatePricing(Request $request, $id)
    {
        $validated = $request->validate([
            'base_price' => 'sometimes|required|numeric|min:0',
            'specs' => 'sometimes|required|array:hide_pricing,quantities,sizes,materials,finishings,custom_specs',
            'specs.hide_pricing' => 'sometimes|boolean',
            'specs.quantities' => 'sometimes|array',
            'specs.sizes' => 'sometimes|array',
            'specs.materials' => 'sometimes|array',
            'specs.finishings' => 'sometimes|array',
            'specs.custom_specs' => 'sometimes|array',
        ]);
        $product = Product::where('is_active', true)->findOrFail($id);
        if (isset($validated['specs'])) {
            $validated['specs'] = array_replace($product->specs ?? [], $validated['specs']);
        }
        $product->update($validated);
        return response()->json(['success' => true, 'data' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $deletedSlug = $product->slug;
        $product->delete();

        if (SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $indexingService->publishUrl('https://dichvuinnhatnguyen.vn/san-pham/' . $deletedSlug, 'URL_DELETED');
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ấn phẩm',
        ]);
    }
}

