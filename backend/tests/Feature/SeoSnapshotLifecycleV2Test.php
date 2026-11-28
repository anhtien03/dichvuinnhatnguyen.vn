<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\PublicSeoSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SeoSnapshotLifecycleV2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        File::deleteDirectory(storage_path('app/private/seo-snapshots'));
        config([
            'seo.snapshots' => true,
            'seo.fallback_html_on_invalid' => false,
            'seo.stale_ttl_seconds' => 900,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('app/private/seo-snapshots'));
        parent::tearDown();
    }

    private function samplePage(string $route, string $title = 'Trang mẫu Nhật Nguyên', string $html = '<main><h1>In Ấn Nhật Nguyên</h1><p>Nội dung hợp lệ.</p></main>'): array
    {
        $canonical = $route === '/'
            ? 'https://dichvuinnhatnguyen.vn/'
            : 'https://dichvuinnhatnguyen.vn/'.ltrim($route, '/');

        return [
            'html' => $html,
            'title' => $title,
            'description' => 'Mô tả trang mẫu cho kiểm thử snapshot V2.',
            'canonical' => $canonical,
            'metadata' => [
                ['property' => 'og:title', 'content' => $title],
            ],
            'schemas' => [
                ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => $title],
            ],
            'bootstrap' => [
                'route' => $route,
                'generatedAt' => time() * 1000,
                'ttlMs' => 300000,
                'responses' => [
                    '/settings' => ['site_name' => 'In Ấn Nhật Nguyên'],
                ],
            ],
        ];
    }

    public function test_scoped_invalidation_isolates_affected_routes_and_reads_with_zero_db_queries(): void
    {
        $category = Category::create([
            'name' => 'Danh mục test',
            'slug' => 'danh-muc-v2',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sản phẩm V2',
            'slug' => 'san-pham-v2',
            'base_price' => 150000,
            'is_active' => true,
        ]);

        $post = Post::create([
            'title' => 'Bài viết V2',
            'slug' => 'bai-viet-v2',
            'content' => 'Nội dung bài viết V2.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $routes = [
            '/',
            '/san-pham',
            '/san-pham/san-pham-v2',
            '/danh-muc/danh-muc-v2',
            '/tin-tuc/bai-viet-v2',
            '/gioi-thieu',
            '/lien-he',
        ];

        foreach ($routes as $route) {
            $rev = PublicSeoSnapshot::routeRevision($route);
            PublicSeoSnapshot::write($route, $rev, $this->samplePage($route));
        }

        // Verify zero DB queries on FRESH snapshot resolution
        DB::enableQueryLog();
        DB::flushQueryLog();
        $resolved = PublicSeoSnapshot::resolve('/san-pham/san-pham-v2');
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertSame(PublicSeoSnapshot::STATE_FRESH, $resolved['state']);
        $this->assertCount(0, $queries);

        // Critical price update on Product
        $product->base_price = 199000;
        $product->save();

        // Affected routes must be INVALID (never serve stale price)
        foreach (['/', '/san-pham', '/san-pham/san-pham-v2', '/danh-muc/danh-muc-v2'] as $affected) {
            $this->assertSame(PublicSeoSnapshot::STATE_INVALID, PublicSeoSnapshot::resolve($affected)['state'], "Expected {$affected} to be INVALID");
        }

        // Unrelated routes must remain FRESH
        foreach (['/tin-tuc/bai-viet-v2', '/gioi-thieu', '/lien-he'] as $unaffected) {
            $this->assertSame(PublicSeoSnapshot::STATE_FRESH, PublicSeoSnapshot::resolve($unaffected)['state'], "Expected {$unaffected} to stay FRESH");
        }

        // Strict 503 when fallback_html_on_invalid is false
        $this->get('/san-pham/san-pham-v2')
            ->assertStatus(503)
            ->assertHeader('X-Seo-Snapshot', PublicSeoSnapshot::STATE_INVALID);

        // Safe HTML fallback when fallback_html_on_invalid is true (must not serve old price)
        config(['seo.fallback_html_on_invalid' => true]);
        $fallbackRes = $this->get('/san-pham/san-pham-v2');
        $fallbackRes->assertStatus(200)
            ->assertHeader('X-Seo-Snapshot', 'FALLBACK');
        $this->assertStringNotContainsString('150000', $fallbackRes->getContent());
        $this->assertStringNotContainsString('id="public-seo-bootstrap"', $fallbackRes->getContent());
    }

    public function test_non_critical_banner_update_serves_stale_within_ttl_without_bootstrap(): void
    {
        $banner = Banner::create([
            'title' => 'Banner ban đầu',
            'image' => '/storage/banner.webp',
            'is_active' => true,
        ]);

        $rev = PublicSeoSnapshot::routeRevision('/');
        PublicSeoSnapshot::write('/', $rev, $this->samplePage('/', 'Trang chủ', '<main><h1>In Ấn Nhật Nguyên</h1></main>'));

        $banner->title = 'Banner cập nhật';
        $banner->save();

        $resolved = PublicSeoSnapshot::resolve('/');
        $this->assertSame(PublicSeoSnapshot::STATE_STALE, $resolved['state']);

        $res = $this->get('/');
        $res->assertStatus(200)
            ->assertHeader('X-Seo-Snapshot', PublicSeoSnapshot::STATE_STALE);
        $this->assertStringNotContainsString('id="public-seo-bootstrap"', $res->getContent());
    }

    public function test_corrupt_checksum_and_tampered_version_return_invalid_503(): void
    {
        $rev = PublicSeoSnapshot::routeRevision('/gioi-thieu');
        PublicSeoSnapshot::write('/gioi-thieu', $rev, $this->samplePage('/gioi-thieu'));

        $file = PublicSeoSnapshot::path('/gioi-thieu');
        $data = json_decode((string) file_get_contents($file), true);
        $data['checksum'] = str_repeat('0', 64);
        file_put_contents($file, json_encode($data));

        $this->assertSame(PublicSeoSnapshot::STATE_INVALID, PublicSeoSnapshot::resolve('/gioi-thieu')['state']);
        $this->get('/gioi-thieu')->assertStatus(503);

        // Rewrite valid, then tamper version
        $rev2 = PublicSeoSnapshot::routeRevision('/gioi-thieu');
        PublicSeoSnapshot::write('/gioi-thieu', $rev2, $this->samplePage('/gioi-thieu'));
        $data2 = json_decode((string) file_get_contents($file), true);
        $data2['version'] = 'tampered:999';
        file_put_contents($file, json_encode($data2));

        $this->assertSame(PublicSeoSnapshot::STATE_INVALID, PublicSeoSnapshot::resolve('/gioi-thieu')['state']);
        $this->get('/gioi-thieu')->assertStatus(503);
    }

    public function test_concurrent_write_version_mismatch_is_rejected(): void
    {
        $staleRev = PublicSeoSnapshot::routeRevision('/lien-he');
        PublicSeoSnapshot::invalidateRoutes(['/lien-he'], false, 'concurrent_mutation');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Public data changed during render');

        PublicSeoSnapshot::write('/lien-he', $staleRev, $this->samplePage('/lien-he'));
    }

    public function test_hidden_product_returns_404_even_if_snapshot_exists(): void
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-hide', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'SP Ẩn',
            'slug' => 'sp-an-v2',
            'base_price' => 50000,
            'is_active' => true,
        ]);

        $rev = PublicSeoSnapshot::routeRevision('/san-pham/sp-an-v2');
        PublicSeoSnapshot::write('/san-pham/sp-an-v2', $rev, $this->samplePage('/san-pham/sp-an-v2'));

        $product->is_active = false;
        $product->save();

        $this->get('/san-pham/sp-an-v2')->assertStatus(404);
    }

    public function test_xss_and_javascript_urls_are_rejected_by_snapshot_writer(): void
    {
        $rev = PublicSeoSnapshot::routeRevision('/gioi-thieu');

        try {
            PublicSeoSnapshot::write('/gioi-thieu', $rev, $this->samplePage('/gioi-thieu', 'XSS', '<main><script>alert(1)</script></main>'));
            $this->fail('Expected script tag in snapshot HTML to be rejected');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Invalid rendered page payload', $e->getMessage());
        }

        try {
            PublicSeoSnapshot::write('/gioi-thieu', $rev, $this->samplePage('/gioi-thieu', 'JS URL', '<main><a href="javascript:alert(1)">Click</a></main>'));
            $this->fail('Expected javascript: URL in snapshot HTML to be rejected');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Invalid rendered page payload', $e->getMessage());
        }
    }

    public function test_public_settings_excludes_sensitive_keys_and_json_ld_escapes_script_tags(): void
    {
        \App\Models\SiteSetting::create(['key' => 'site_name', 'value' => 'In Ấn Nhật Nguyên']);
        \App\Models\SiteSetting::create(['key' => 'google_indexing_credentials_json', 'value' => '{"private_key":"SECRET"}']);
        \App\Models\SiteSetting::create(['key' => 'webhook_secret', 'value' => 'super-secret-value']);

        $res = $this->getJson('/api/v1/settings');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertSame('In Ấn Nhật Nguyên', $data['site_name'] ?? null);
        $this->assertArrayNotHasKey('google_indexing_credentials_json', $data);
        $this->assertArrayNotHasKey('webhook_secret', $data);

        // JSON-LD & bootstrap script breakout escaping
        $rev = PublicSeoSnapshot::routeRevision('/gioi-thieu');
        $page = $this->samplePage('/gioi-thieu', '</script><script>alert(1)</script>');
        $page['schemas'] = [['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => '</script><script>alert(2)</script>']];
        PublicSeoSnapshot::write('/gioi-thieu', $rev, $page);

        $html = $this->get('/gioi-thieu')->assertStatus(200)->getContent();
        $this->assertStringNotContainsString('</script><script>alert(', $html);
    }

    public function test_path_traversal_and_non_loopback_render_origin_are_blocked(): void
    {
        $this->get('/san-pham/../../etc/passwd')->assertStatus(404);
        $this->artisan('seo:render', ['--origin' => 'https://evil.example'])->assertExitCode(1);
    }

    public function test_public_settings_excludes_service_account_json_and_update_succeeds_without_error(): void
    {
        SiteSetting::create(['key' => 'site_name', 'value' => 'In Ấn Nhật Nguyên']);
        SiteSetting::create([
            'key' => 'google_indexing_service_account_json',
            'value' => '{"type":"service_account","private_key":"-----BEGIN PRIVATE KEY-----\\nSECRET"}',
        ]);

        $publicRes = $this->getJson('/api/v1/settings')->assertStatus(200);
        $this->assertArrayNotHasKey('google_indexing_service_account_json', $publicRes->json('data'));

        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-v2@example.test',
            'password' => 'secret-password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
        Sanctum::actingAs($admin);

        $updateRes = $this->postJson('/api/v1/admin/settings', [
            'site_name' => 'In Ấn Nhật Nguyên Updated',
        ])->assertStatus(200);

        $updateRes->assertJsonPath('success', true);
        $updateRes->assertJsonPath('data.site_name', 'In Ấn Nhật Nguyên Updated');
        $this->assertArrayHasKey('google_indexing_service_account_json', $updateRes->json('data'));
    }

    public function test_transaction_slug_and_category_change_invalidates_old_urls(): void
    {
        $catA = Category::create(['name' => 'Danh mục A', 'slug' => 'cat-a-v2', 'is_active' => true]);
        $catB = Category::create(['name' => 'Danh mục B', 'slug' => 'cat-b-v2', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $catA->id,
            'name' => 'Sản phẩm TX',
            'slug' => 'prod-old-v2',
            'base_price' => 100000,
            'is_active' => true,
        ]);

        foreach (['/san-pham/prod-old-v2', '/san-pham/prod-new-v2', '/danh-muc/cat-a-v2', '/danh-muc/cat-b-v2'] as $route) {
            $rev = PublicSeoSnapshot::routeRevision($route);
            PublicSeoSnapshot::write($route, $rev, $this->samplePage($route));
        }

        DB::transaction(static function () use ($product, $catB): void {
            $product->slug = 'prod-new-v2';
            $product->category_id = $catB->id;
            $product->save();
        });

        foreach (['/san-pham/prod-old-v2', '/san-pham/prod-new-v2', '/danh-muc/cat-a-v2', '/danh-muc/cat-b-v2'] as $route) {
            $this->assertSame(
                PublicSeoSnapshot::STATE_INVALID,
                PublicSeoSnapshot::resolve($route)['state'],
                "Expected {$route} to be INVALID after transaction"
            );
        }
    }

    public function test_product_controller_sanitizes_xss_in_description(): void
    {
        SiteSetting::set('google_indexing_auto_publish', false);
        $category = Category::create(['name' => 'Cat XSS', 'slug' => 'cat-xss', 'is_active' => true]);
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);
        $admin = User::create([
            'name' => 'Admin XSS',
            'email' => 'admin-xss@example.test',
            'password' => 'secret-password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
        Sanctum::actingAs($admin);

        $res = $this->postJson('/api/v1/admin/products', [
            'name' => 'Sản phẩm XSS',
            'slug' => 'san-pham-xss',
            'category_id' => $category->id,
            'base_price' => 50000,
            'unit' => 'cái',
            'description' => '<p>Mô tả hợp lệ</p><img src=x onerror=alert(1)><img/onload=alert(2)><svg onload=alert(3)><script>alert(4)</script></svg>',
        ])->assertStatus(201);

        $desc = (string) $res->json('data.description');
        $this->assertStringContainsString('<p>Mô tả hợp lệ</p>', $desc);
        $this->assertStringNotContainsString('onerror', strtolower($desc));
        $this->assertStringNotContainsString('onload', strtolower($desc));
        $this->assertStringNotContainsString('<svg', strtolower($desc));
        $this->assertStringNotContainsString('<script', strtolower($desc));
    }
}
