<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PermissionSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }

    private function staff(array $permissions = [], bool $active = true): User
    {
        $user = new User(['is_active' => $active, 'custom_permissions' => $permissions]);
        $user->setRelation('role', null);
        Sanctum::actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_admin_endpoints(): void
    {
        foreach (['/admin/dashboard-stats', '/admin/users', '/admin/quotes', '/admin/erp/orders', '/admin/erp/finance/dashboard'] as $path) {
            $this->getJson('/api/v1'.$path)->assertUnauthorized();
        }
        $this->putJson('/api/v1/admin/products/1', [])->assertUnauthorized();
    }

    public function test_staff_without_permissions_cannot_access_protected_operations(): void
    {
        $this->staff();
        foreach (['/admin/users', '/admin/quotes', '/admin/erp/orders', '/admin/erp/customers', '/admin/erp/suppliers', '/admin/erp/cashbook', '/admin/erp/finance/dashboard', '/admin/erp/finance/profit-sharing'] as $path) {
            $this->getJson('/api/v1'.$path)->assertForbidden();
        }
        $this->putJson('/api/v1/admin/products/1', ['base_price' => 1200])->assertForbidden();
        $this->putJson('/api/v1/admin/users/1', [])->assertForbidden();
        $this->postJson('/api/v1/admin/erp/orders/1/payments', ['amount' => 1000])->assertForbidden();
        $this->deleteJson('/api/v1/admin/erp/orders/1')->assertForbidden();
    }

    public function test_inactive_staff_is_denied_even_with_permission(): void
    {
        $this->staff(['products.manage' => true, 'orders.view' => true], false);
        $this->putJson('/api/v1/admin/products/1', [])->assertForbidden();
        $this->getJson('/api/v1/admin/erp/orders')->assertForbidden();
    }

    public function test_explicit_permission_overrides(): void
    {
        $user = $this->staff(['pricing.manage' => true, 'products.manage' => false]);
        $this->assertTrue($user->hasPermission('pricing.manage'));
        $this->assertFalse($user->hasPermission('products.manage'));
        $this->assertFalse($user->hasPermission('users.manage'));
    }

    // Dedicated pricing route and type-specific SEO authorization.
    public function test_pricing_only_manager_must_not_be_blocked_by_product_permission(): void
    {
        $this->staff(['pricing.manage' => true]);
        $route = app('router')->getRoutes()->match(\Illuminate\Http\Request::create('/api/v1/admin/products/1/pricing', 'PUT'));
        $this->assertContains('permission:pricing.manage', $route->gatherMiddleware(), 'Dedicated pricing endpoint must enforce pricing.manage.');
    }

    public function test_seo_endpoint_has_business_permission(): void
    {
        config(['services.gemini.api_key' => '']);
        \Illuminate\Support\Facades\Http::fake();
        $url = '/api/v1/admin/seo/optimize';
        $this->staff();
        $this->postJson($url, ['title'=>'Fixture'])->assertForbidden();
        foreach (['product'=>'products.manage','post'=>'posts.manage','category'=>'categories.manage'] as $type=>$permission) {
            $this->staff([$permission=>true]);
            $this->postJson($url, ['title'=>'Fixture','type'=>$type])->assertStatus(503);
            foreach (['product','post','category'] as $other) {
                if ($other !== $type) $this->postJson($url, ['title'=>'Fixture','type'=>$other])->assertForbidden();
            }
        }
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }
}
