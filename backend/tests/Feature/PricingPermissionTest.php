<?php
namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PricingPermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Schema::create('categories', function(Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('parent_id')->nullable();
        });
        Schema::create('products', function(Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('category_id')->nullable();
            $t->string('slug')->unique(); $t->string('name'); $t->string('unit')->default('cai');
            $t->integer('base_price')->default(0); $t->text('specs')->nullable();
            $t->boolean('is_active')->default(true); $t->integer('sort_order')->default(0); $t->timestamps();
        });
        Schema::create('site_settings', function(Blueprint $t) {
            $t->id(); $t->string('key'); $t->text('value'); $t->timestamps();
        });
        DB::table('site_settings')->insert(['key'=>'google_indexing_auto_publish','value'=>'false']);
        Product::create(['name'=>'Fixture','slug'=>'fixture','base_price'=>1000,'is_active'=>true,'specs'=>['hide_pricing'=>false,'quantities'=>[['count'=>100,'pricePerUnit'=>1000]],'custom_specs'=>[['name'=>'Paper','value'=>'A4']]]]);
        Product::create(['name'=>'Hidden','slug'=>'hidden','is_active'=>false]);
    }

    private function staff(array $permissions, bool $active = true): void
    {
        $u = new User(['is_active'=>$active,'custom_permissions'=>$permissions]);
        $u->setRelation('role', null);
        Sanctum::actingAs($u);
    }

    public function test_guest_cannot_write_pricing(): void
    {
        $this->putJson('/api/v1/admin/products/1/pricing',['base_price'=>1200])->assertUnauthorized();
    }

    public function test_product_manager_cannot_change_price(): void
    {
        $this->staff(['products.manage'=>true]);
        $before = Product::findOrFail(1)->specs;
        $this->putJson('/api/v1/admin/products/1',['base_price'=>1200])->assertForbidden();
        $this->putJson('/api/v1/admin/products/1',['specs'=>null])->assertForbidden();
        $this->putJson('/api/v1/admin/products/1/pricing',['base_price'=>1200])->assertForbidden();
        $this->assertSame(1000, Product::findOrFail(1)->base_price);
        $this->assertSame($before, Product::findOrFail(1)->specs);
    }

    public function test_product_metadata_update_preserves_pricing(): void
    {
        $this->staff(['products.manage'=>true]);
        $before = Product::findOrFail(1)->specs;
        $this->putJson('/api/v1/admin/products/1',['name'=>'Renamed'])->assertOk();
        $p = Product::findOrFail(1);
        $this->assertSame('Renamed',$p->name);
        $this->assertSame(1000,$p->base_price);
        $this->assertSame($before,$p->specs);
    }

    public function test_pricing_manager_can_update_price_but_not_metadata(): void
    {
        $this->staff(['pricing.manage'=>true]);
        $this->putJson('/api/v1/admin/products/1/pricing',['base_price'=>1200,'name'=>'Unauthorized','specs'=>['hide_pricing'=>true]])->assertOk();
        $p = Product::findOrFail(1);
        $this->assertSame(1200,$p->base_price);
        $this->assertSame('Fixture',$p->name);
        $this->assertTrue($p->specs['hide_pricing']);
        $this->assertSame(1000,$p->specs['quantities'][0]['pricePerUnit']);
        $this->assertSame('A4',$p->specs['custom_specs'][0]['value']);
        $this->putJson('/api/v1/admin/products/1',['name'=>'Unauthorized'])->assertForbidden();
        $this->deleteJson('/api/v1/admin/products/1')->assertForbidden();
    }

    public function test_product_creation_without_pricing_permission_is_contact_only(): void
    {
        $this->staff(['products.manage'=>true]);
        $this->postJson('/api/v1/admin/products',['name'=>'New fixture','slug'=>'new-fixture','unit'=>'cai','base_price'=>9999,'specs'=>['hide_pricing'=>false,'quantities'=>[['count'=>1,'pricePerUnit'=>9999]]]])->assertCreated();
        $p = Product::where('slug','new-fixture')->firstOrFail();
        $this->assertSame(0,$p->base_price);
        $this->assertTrue($p->specs['hide_pricing']);
        $this->assertArrayNotHasKey('quantities',$p->specs);
    }

    public function test_inactive_staff_and_hidden_products_are_denied(): void
    {
        $this->staff(['pricing.manage'=>true],false);
        $this->putJson('/api/v1/admin/products/1/pricing',['base_price'=>1200])->assertForbidden();
        $this->staff(['pricing.manage'=>true]);
        $this->putJson('/api/v1/admin/products/2/pricing',['base_price'=>1200])->assertNotFound();
    }

    public function test_public_price_remains_readable(): void
    {
        $this->getJson('/api/v1/products/fixture')->assertOk()->assertJsonPath('data.base_price',1000)->assertJsonPath('data.specs.quantities.0.pricePerUnit',1000);
    }
}
