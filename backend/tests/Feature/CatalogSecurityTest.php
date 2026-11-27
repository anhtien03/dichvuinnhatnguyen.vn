<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CatalogSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('slug');
            $table->string('name');
            $table->boolean('is_active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        Product::create(['slug'=>'public-fixture','name'=>'Public','is_active'=>true]);
        Product::create(['slug'=>'hidden-fixture','name'=>'Hidden','is_active'=>false]);
    }

    public function test_anonymous_cannot_read_hidden_products(): void
    {
        $this->getJson('/api/v1/products?all=1')->assertOk()->assertJsonCount(1,'data');
        $this->getJson('/api/v1/products/hidden-fixture?all=1')->assertNotFound();
        $this->getJson('/api/v1/products/2?all=1')->assertNotFound();
        $this->getJson('/api/v1/products/public-fixture')->assertOk();
    }

    public function test_authorized_manager_can_read_hidden_products(): void
    {
        $user = new User(['is_active'=>true,'custom_permissions'=>['products.manage'=>true]]);
        $user->setRelation('role', null);
        Sanctum::actingAs($user);
        $this->getJson('/api/v1/products?all=1')->assertOk()->assertJsonCount(2,'data');
        $this->getJson('/api/v1/products/hidden-fixture?all=1')->assertOk();
        $this->getJson('/api/v1/products/hidden-fixture')->assertNotFound();
    }

    public function test_staff_without_permission_cannot_read_hidden_products(): void
    {
        $user = new User(['is_active'=>true,'custom_permissions'=>['products.manage'=>false]]);
        $user->setRelation('role', null);
        Sanctum::actingAs($user);
        $this->getJson('/api/v1/products?all=1')->assertOk()->assertJsonCount(1,'data');
        $this->getJson('/api/v1/products/2?all=1')->assertNotFound();
    }

    public function test_catalog_limit_logs_once_and_preserves_detail_budget(): void
    {
        $logger = \Mockery::mock(\Psr\Log\LoggerInterface::class);
        $logger->shouldReceive('warning')->once()->with('api_rate_limit_exceeded', \Mockery::on(function ($context) {
            return $context['limiter'] === 'catalog-list'
                && strlen($context['ip_fingerprint']) === 64
                && array_keys($context) === ['limiter','ip_fingerprint','method'];
        }));
        Log::shouldReceive('channel')->with('security')->andReturn($logger);
        for ($i=0; $i<120; $i++) {
            $this->getJson('/api/v1/products')->assertOk();
        }
        $this->getJson('/api/v1/products')->assertStatus(429)->assertHeader('Retry-After');
        $this->getJson('/api/v1/products')->assertStatus(429);
        $this->getJson('/api/v1/products/public-fixture')->assertOk();
    }
}
