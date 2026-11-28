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
        $record = null;
        $exists = in_array($path, $static, true);
        if (preg_match('~^(san-pham|danh-muc|tin-tuc)/([^/]+)$~u', $path, $parts)) {
            $record = match ($parts[1]) {
                'san-pham' => Product::where('slug', $parts[2])->where('is_active', true)->first(),
                'danh-muc' => Category::where('slug', $parts[2])->where('is_active', true)->first(),
                'tin-tuc' => Post::where('slug', $parts[2])->where('is_published', true)->first(),
            };
            $exists = $record !== null;
        }
        $admin = $path === 'he-thong-admin' || str_starts_with($path, 'he-thong-admin/');
        $file = base_path('../dist/index.html');
        abort_unless(is_readable($file), 503, 'Frontend build unavailable');
        $html = file_get_contents($file);
        abort_if($html === false, 503, 'Frontend build unavailable');
        $titles = [
            '' => 'Trang chủ - In Ấn Nhật Nguyên',
            'san-pham' => 'Danh Mục Sản Phẩm & Dịch Vụ In Ấn Trực Tiếp Tại Xưởng | Nhật Nguyên',
            'tin-tuc' => 'Cẩm nang & Kiến thức ngành in',
            'gioi-thieu' => 'Giới thiệu xưởng in Nhật Nguyên - Nhanh, Đẹp, Giá Tốt',
            'lien-he' => 'Liên hệ xưởng in Nhật Nguyên',
            'yeu-cau-bao-gia' => 'Gửi yêu cầu báo giá nhanh',
        ];
        $title = $titles[$path] ?? '404 - Trang không tồn tại';
        $description = 'Xưởng in Offset & Kỹ thuật số Nhật Nguyên chuyên in hộp giấy, túi giấy, tem nhãn decal, catalogue, namecard giá gốc tận xưởng.';
        if ($record) {
            $title = $record->meta_title ?: match ($parts[1]) {
                'san-pham' => $record->name . ' - Giá Gốc Xưởng In Nhật Nguyên',
                'danh-muc' => $record->name . ' - Giá Gốc Trực Tiếp Tại Xưởng TPHCM | Nhật Nguyên',
                'tin-tuc' => $record->title . ' - Cẩm Nang In Ấn',
            };
            $description = $record->meta_description ?: strip_tags($record->description ?? $record->excerpt ?? '') ?: $description;
        }
        if ($admin) $title = 'Quản trị - In Ấn Nhật Nguyên';
        if (!str_contains($title, 'Nhật Nguyên')) $title .= ' | In Ấn Nhật Nguyên';
        $canonical = 'https://dichvuinnhatnguyen.vn/' . implode('/', array_map('rawurlencode', explode('/', $path)));
        $robots = $admin ? 'noindex, nofollow' : ((!$exists || ($path === 'yeu-cau-bao-gia' && $request->has('product'))) ? 'noindex, follow' : 'index, follow');
        $html = preg_replace_callback('~<title>.*?</title>~is', fn () => '<title>'.e($title).'</title>', $html, 1);
        $html = preg_replace_callback('~<meta\\s+name="description"[^>]*>~i', fn () => '<meta name="description" content="'.e($description).'">', $html, 1);
        $head = '<link rel="canonical" href="'.e($canonical).'"><meta name="robots" content="'.e($robots).'">';
        $html = str_replace('</head>', $head.'</head>', $html);
        $snapshotState = null;
        if ($exists && !$admin && !($path === 'yeu-cau-bao-gia' && $request->has('product')) && config('seo.snapshots')) {
            $routeKey = $path === '' ? '/' : '/'.implode('/', array_map('rawurlencode', explode('/', $path)));
            $resolved = \App\Services\PublicSeoSnapshot::resolve($routeKey);
            $snapshotState = $resolved['state'];
            $snapshot = in_array($snapshotState, [\App\Services\PublicSeoSnapshot::STATE_FRESH, \App\Services\PublicSeoSnapshot::STATE_STALE], true)
                ? $resolved['page']
                : null;

            if (!$snapshot) {
                if (!config('seo.fallback_html_on_invalid', false)) {
                    return response('Trang đang được cập nhật. Vui lòng thử lại sau.', 503)
                        ->header('Retry-After', '60')
                        ->header('Cache-Control', 'no-store')
                        ->header('X-Seo-Snapshot', $snapshotState);
                }
                $snapshotState = 'FALLBACK';
            } else {
                $html = preg_replace_callback('~<div id="app"></div>~', fn () => '<div id="app">'.$snapshot['html'].'</div>', $html, 1, $count);
                abort_unless($count === 1, 503, 'Frontend shell mismatch');
                $html = preg_replace_callback('~<title>.*?</title>~is', fn () => '<title>'.e($snapshot['title']).'</title>', $html, 1);
                $html = preg_replace_callback('~<meta\\s+name="description"[^>]*>~i', fn () => '<meta name="description" content="'.e($snapshot['description']).'">', $html, 1);
                $html = preg_replace_callback('~<link rel="canonical"[^>]*>~i', fn () => '<link rel="canonical" href="'.e($snapshot['canonical']).'">', $html, 1);
                $extra = '';
                // Never embed stale bootstrap pricing data into STALE snapshots.
                if ($snapshotState === \App\Services\PublicSeoSnapshot::STATE_FRESH && !empty($snapshot['bootstrap'])) {
                    $extra .= '<script type="application/json" id="public-seo-bootstrap">'.json_encode($snapshot['bootstrap'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR).'</script>';
                }
                foreach ($snapshot['metadata'] ?? [] as $meta) {
                    $key = $meta['property'] ?? $meta['name'] ?? '';
                    if (!preg_match('~^(og:|twitter:)[a-z0-9_:]+$~i', $key)) continue;
                    $attribute = $meta['property'] ? 'property' : 'name';
                    $extra .= '<meta '.$attribute.'="'.e($key).'" content="'.e($meta['content']).'">';
                }
                foreach ($snapshot['schemas'] ?? [] as $schema) {
                    $extra .= '<script type="application/ld+json" data-seo-snapshot="true">'.json_encode($schema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR).'</script>';
                }
                $html = str_replace('</head>', $extra.'</head>', $html);
            }
        }
        $response = response($html, ($exists || $admin) ? 200 : 404)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-store');
        if ($snapshotState !== null) {
            $response->header('X-Seo-Snapshot', $snapshotState);
        }
        if (!$exists || $admin || ($path === 'yeu-cau-bao-gia' && $request->has('product'))) {
            $response->header('X-Robots-Tag', $admin ? 'noindex, nofollow' : 'noindex, follow');
        }
        return $response;
    }
}
