<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Services\GoogleIndexingService;
use App\Services\SecuritySanitizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        // Optional filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        // Optional search by keyword
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // For public requests (unauthenticated or all=false), only show published
        if (!$request->user() && !$request->boolean('all')) {
            $query->where('is_published', true);
        }

        $posts = $query->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    /**
     * Display the specified post by slug or ID.
     */
    public function show($slugOrId)
    {
        $post = is_numeric($slugOrId)
            ? Post::findOrFail($slugOrId)
            : Post::where('slug', $slugOrId)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    /**
     * Store a newly created post with security sanitization.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Post::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $validated['category'] = $validated['category'] ?? 'Cẩm nang in ấn';
        $validated['is_published'] = $validated['is_published'] ?? true;
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Attach author from session
        $user = $request->user();
        if ($user) {
            $validated['author_id'] = $user->id;
            $validated['author_name'] = $user->name;
        }

        // 🛡️ SECURITY SANITIZATION (Strip malicious script, iframe, XSS, and enforce nofollow on external links)
        if (isset($validated['content'])) {
            $validated['content'] = SecuritySanitizerService::sanitizeHtml($validated['content']);
        }
        if (isset($validated['title'])) {
            $validated['title'] = SecuritySanitizerService::sanitizeText($validated['title']);
        }
        if (isset($validated['excerpt'])) {
            $validated['excerpt'] = SecuritySanitizerService::sanitizeText($validated['excerpt']);
        }

        $post = Post::create($validated);

        if ($post->is_published && SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $indexingService->publishUrl('https://dichvuinnhatnguyen.vn/tin-tuc/' . $post->slug, 'URL_UPDATED');
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng bài viết mới thành công',
            'data' => $post,
        ], 201);
    }

    /**
     * Update the specified post with security sanitization.
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        // 🛡️ AUTHOR ISOLATION FOR OUTSOURCED WRITERS
        if ($user && $user->role && $user->role->slug === 'writer') {
            if ($post->author_id && $post->author_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chỉ có quyền chỉnh sửa bài viết do chính tài khoản của bạn viết.',
                ], 403);
            }
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => "sometimes|required|string|max:255|unique:posts,slug,{$id}",
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if (isset($validated['is_published']) && $validated['is_published'] && empty($post->published_at)) {
            $validated['published_at'] = now();
        }

        // 🛡️ SECURITY SANITIZATION
        if (isset($validated['content'])) {
            $validated['content'] = SecuritySanitizerService::sanitizeHtml($validated['content']);
        }
        if (isset($validated['title'])) {
            $validated['title'] = SecuritySanitizerService::sanitizeText($validated['title']);
        }
        if (isset($validated['excerpt'])) {
            $validated['excerpt'] = SecuritySanitizerService::sanitizeText($validated['excerpt']);
        }

        $post->update($validated);

        if ($post->is_published && SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $indexingService->publishUrl('https://dichvuinnhatnguyen.vn/tin-tuc/' . $post->slug, 'URL_UPDATED');
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài viết thành công',
            'data' => $post,
        ]);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        // 🛡️ PROTECT PILLAR POSTS: Freelancer cannot delete other authors' posts or live published articles
        if ($user && $user->role && $user->role->slug === 'writer') {
            if ($post->author_id && $post->author_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể xóa bài viết của tác giả khác.',
                ], 403);
            }
            if ($post->is_published) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài viết đã xuất bản chỉ có Ban Quản Trị mới có thể xóa để bảo toàn SEO.',
                ], 403);
            }
        }

        $deletedSlug = $post->slug;
        $post->delete();

        if (SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $indexingService->publishUrl('https://dichvuinnhatnguyen.vn/tin-tuc/' . $deletedSlug, 'URL_DELETED');
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Xóa bài viết thành công',
        ]);
    }

    /**
     * Toggle publish status.
     */
    public function togglePublish(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $user = $request->user();

        // If writer, cannot toggle published status of other authors
        if ($user && $user->role && $user->role->slug === 'writer' && $post->author_id && $post->author_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có quyền thay đổi trạng thái bài viết của chính mình.',
            ], 403);
        }

        $post->is_published = !$post->is_published;
        if ($post->is_published && empty($post->published_at)) {
            $post->published_at = now();
        }
        $post->save();

        if (SiteSetting::get('google_indexing_auto_publish', true)) {
            try {
                $indexingService = app(GoogleIndexingService::class);
                $action = $post->is_published ? 'URL_UPDATED' : 'URL_DELETED';
                $indexingService->publishUrl('https://dichvuinnhatnguyen.vn/tin-tuc/' . $post->slug, $action);
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => $post->is_published ? 'Đã xuất bản bài viết' : 'Đã chuyển bài viết về bản nháp',
            'data' => $post,
        ]);
    }
}
