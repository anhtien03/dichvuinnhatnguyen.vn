<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of banners.
     */
    public function index(Request $request)
    {
        $query = Banner::query();

        // Optional filter by position (e.g. home_main, home_sub_1)
        if ($request->filled('position')) {
            $query->where('position', $request->query('position'));
        }

        // For public endpoints, only show active banners
        if (!$request->user() && !$request->boolean('all')) {
            $query->where('is_active', true);
        }

        $banners = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'badge' => 'nullable|string|max:100',
            'image_url' => 'nullable|string',
            'mobile_image_url' => 'nullable|string',
            'link_url' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'extra_text' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:50',
            'bg_color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['position'] = $validated['position'] ?? 'home_main';
        $validated['bg_color'] = $validated['bg_color'] ?? 'blue';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['button_text'] = $validated['button_text'] ?? 'Xem Chi Tiết';

        $banner = Banner::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo banner mới thành công',
            'data' => $banner,
        ], 201);
    }

    /**
     * Display the specified banner.
     */
    public function show($id)
    {
        $banner = Banner::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $banner,
        ]);
    }

    /**
     * Update the specified banner.
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'badge' => 'nullable|string|max:100',
            'image_url' => 'nullable|string',
            'mobile_image_url' => 'nullable|string',
            'link_url' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'extra_text' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:50',
            'bg_color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $banner->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật banner thành công',
            'data' => $banner,
        ]);
    }

    /**
     * Remove the specified banner.
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa banner thành công',
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return response()->json([
            'success' => true,
            'message' => $banner->is_active ? 'Đã kích hoạt banner' : 'Đã ẩn banner',
            'data' => $banner,
        ]);
    }

    /**
     * Reorder banners.
     */
    public function reorder(Request $request)
    {
        $items = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer|exists:banners,id',
            'orders.*.sort_order' => 'required|integer',
        ])['orders'];

        foreach ($items as $item) {
            Banner::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thứ tự hiển thị banner',
        ]);
    }
}

