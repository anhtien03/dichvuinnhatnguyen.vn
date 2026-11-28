<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardContentSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        foreach (['products','categories','posts','banners','quote_requests','erp_orders','erp_customers','erp_order_items'] as $table) {
            Schema::create($table, function(Blueprint $t) {
                $t->id(); $t->string('slug')->nullable(); $t->string('name')->nullable();
                $t->unsignedBigInteger('parent_id')->nullable(); $t->unsignedBigInteger('category_id')->nullable();
                $t->boolean('is_active')->default(true); $t->boolean('is_published')->default(true);
                $t->integer('sort_order')->default(0); $t->timestamp('published_at')->nullable();
                $t->string('status')->default('pending'); $t->unsignedBigInteger('product_id')->nullable();
                $t->unsignedBigInteger('order_id')->nullable(); $t->string('product_name')->nullable();
                $t->integer('quantity')->default(1); $t->timestamps();
            });
        }
        foreach (['categories','posts','banners','products'] as $table) {
            DB::table($table)->insert([
                ['id'=>1,'slug'=>'visible','name'=>'Visible','is_active'=>true,'is_published'=>true,'category_id'=>1],
                ['id'=>2,'slug'=>'hidden','name'=>'Hidden','is_active'=>false,'is_published'=>false,'category_id'=>1],
            ]);
        }
        DB::table('categories')->where('id',2)->update(['parent_id'=>1]);
        DB::table('quote_requests')->insert(['id'=>1,'status'=>'pending']);
        DB::table('erp_orders')->insert(['id'=>1,'created_at'=>now()]);
        DB::table('erp_customers')->insert(['id'=>1,'created_at'=>now()]);
        DB::table('erp_order_items')->insert(['order_id'=>1,'product_name'=>'Fixture','quantity'=>5]);
    }
    private function staff(array $permissions = [], bool $active = true): void
    {
        $u = new User(['is_active'=>$active,'custom_permissions'=>$permissions]);
        $u->setRelation('role',null);
        Sanctum::actingAs($u);
    }
    public function test_public_lists_and_details_exclude_hidden_records(): void
    {
        foreach (['categories','posts','banners'] as $resource) {
            $this->getJson('/api/v1/'.$resource.'?all=1')->assertOk()->assertJsonCount(1,'data')->assertJsonPath('data.0.id',1);
        }
        foreach (['categories','posts'] as $resource) {
            $this->getJson('/api/v1/'.$resource.'/hidden?all=1')->assertNotFound();
            $this->getJson('/api/v1/'.$resource.'/2?all=1')->assertNotFound();
            $this->getJson('/api/v1/'.$resource.'/visible')->assertOk();
        }
        $this->getJson('/api/v1/categories/visible')->assertOk()->assertJsonPath('data.products_count',1)->assertJsonCount(0,'data.children')->assertJsonPath('data.children_count',0);
    }
    public function test_admin_content_requires_matching_permission(): void
    {
        $this->staff();
        foreach (['categories','posts','banners'] as $resource) {
            $this->getJson('/api/v1/admin/'.$resource)->assertForbidden();
        }
        $this->staff(['categories.manage'=>true,'posts.manage'=>true,'banners.manage'=>true]);
        foreach (['categories','posts','banners'] as $resource) {
            $this->getJson('/api/v1/admin/'.$resource)->assertOk()->assertJsonCount(2,'data');
            $this->getJson('/api/v1/admin/'.$resource.'/2')->assertOk();
            $this->getJson('/api/v1/'.$resource.'?all=1')->assertOk()->assertJsonCount(1,'data');
        }
    }
    public function test_inactive_staff_cannot_read_dashboard(): void
    {
        $this->staff(['quotes.view'=>true],false);
        $this->getJson('/api/v1/admin/dashboard-stats')->assertForbidden();
    }
    public function test_dashboard_without_permissions_returns_no_private_data(): void
    {
        $this->staff();
        $r=$this->getJson('/api/v1/admin/dashboard-stats')->assertOk();
        foreach (['quotes','products','categories','posts','banners'] as $key) {
            $r->assertJsonPath('data.'.$key.'.total',0);
        }
        $r->assertJsonCount(0,'data.recent_quotes')->assertJsonCount(0,'data.recent_posts')->assertJsonCount(0,'data.top_products')->assertJsonPath('data.total_order_items',0);
        foreach ($r->json('data.chart_series') as $series) {
            foreach ($series as $row) {
                $this->assertSame(0,$row['orders']);
                $this->assertSame(0,$row['customers']);
            }
        }
    }
    public function test_dashboard_permissions_are_independent(): void
    {
        $this->staff(['quotes.view'=>true,'orders.view'=>true]);
        $r=$this->getJson('/api/v1/admin/dashboard-stats')->assertOk()->assertJsonPath('data.quotes.total',1)->assertJsonCount(1,'data.recent_quotes')->assertJsonPath('data.total_order_items',1)->assertJsonCount(1,'data.top_products');
        $this->assertSame(1,array_sum(array_column($r->json('data.chart_series.day'),'orders')));
        $this->assertSame(0,array_sum(array_column($r->json('data.chart_series.day'),'customers')));
        $this->staff(['clients.view'=>true,'products.manage'=>true,'categories.manage'=>true,'posts.manage'=>true,'banners.manage'=>true]);
        $r=$this->getJson('/api/v1/admin/dashboard-stats')->assertOk()->assertJsonPath('data.quotes.total',0)->assertJsonPath('data.total_order_items',0);
        foreach (['products','categories','posts','banners'] as $key) $r->assertJsonPath('data.'.$key.'.total',2);
        $this->assertSame(0,array_sum(array_column($r->json('data.chart_series.day'),'orders')));
        $this->assertSame(1,array_sum(array_column($r->json('data.chart_series.day'),'customers')));
    }
}
