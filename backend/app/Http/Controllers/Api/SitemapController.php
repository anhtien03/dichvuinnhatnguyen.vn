<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Dynamically generate XML sitemap for public pages, active categories/products, and published posts.
     */
    public function index(): Response
    {
        $baseUrl = 'https://dichvuinnhatnguyen.vn';

        $urls = [
            ['loc' => "{$baseUrl}/", 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => "{$baseUrl}/san-pham", 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => "{$baseUrl}/yeu-cau-bao-gia", 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => "{$baseUrl}/tin-tuc", 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => "{$baseUrl}/gioi-thieu", 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => "{$baseUrl}/lien-he", 'changefreq' => 'monthly', 'priority' => '0.7'],
        ];

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('id')
            ->get(['slug', 'updated_at']);

        foreach ($categories as $cat) {
            $urls[] = [
                'loc' => "{$baseUrl}/danh-muc/" . rawurlencode($cat->slug),
                'lastmod' => optional($cat->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        $products = Product::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        foreach ($products as $product) {
            $urls[] = [
                'loc' => "{$baseUrl}/san-pham/" . rawurlencode($product->slug),
                'lastmod' => optional($product->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        $posts = Post::query()
            ->where('is_published', true)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderByDesc('updated_at')
            ->get(['slug', 'published_at', 'updated_at']);

        foreach ($posts as $post) {
            $lastmod = $post->updated_at ?? $post->published_at;
            $urls[] = [
                'loc' => "{$baseUrl}/tin-tuc/" . rawurlencode($post->slug),
                'lastmod' => optional($lastmod)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        $xml = $this->renderXml($urls);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }

    /**
     * Render a valid XML sitemap string.
     */
    private function renderXml(array $urls): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($urls as $item) {
            $loc = htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8');
            $lines[] = '  <url>';
            $lines[] = "    <loc>{$loc}</loc>";
            if (!empty($item['lastmod'])) {
                $lastmod = htmlspecialchars($item['lastmod'], ENT_XML1, 'UTF-8');
                $lines[] = "    <lastmod>{$lastmod}</lastmod>";
            }
            if (!empty($item['changefreq'])) {
                $lines[] = "    <changefreq>{$item['changefreq']}</changefreq>";
            }
            if (!empty($item['priority'])) {
                $lines[] = "    <priority>{$item['priority']}</priority>";
            }
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines) . "\n";
    }
}
