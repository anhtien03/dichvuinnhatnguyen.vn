<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Post;
use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    public function __invoke(Request $request)
    {
        $path = trim($request->path(), '/');
        $static = ['', 'san-pham', 'tin-tuc', 'gioi-thieu', 'lien-he', 'yeu-cau-bao-gia'];
        $exists = in_array($path, $static, true);
        if (preg_match('~^(san-pham|danh-muc|tin-tuc)/([^/]+)$~u', $path, $parts)) {
            $exists = match ($parts[1]) {
                'san-pham' => Product::where('slug', $parts[2])->where('is_active', true)->exists(),
                'danh-muc' => Category::where('slug', $parts[2])->where('is_active', true)->exists(),
                'tin-tuc' => Post::where('slug', $parts[2])->where('is_published', true)->exists(),
            };
        }
        $admin = $path === 'he-thong-admin' || str_starts_with($path, 'he-thong-admin/');
        $file = base_path('../dist/index.html');
        abort_unless(is_readable($file), 503, 'Frontend build unavailable');
        $html = file_get_contents($file);
        abort_if($html === false, 503, 'Frontend build unavailable');
        $response = response($html, ($exists || $admin) ? 200 : 404)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-store');
        if (!$exists || $admin || ($path === 'yeu-cau-bao-gia' && $request->has('product'))) {
            $response->header('X-Robots-Tag', $admin ? 'noindex, nofollow' : 'noindex, follow');
        }
        return $response;
    }
}
