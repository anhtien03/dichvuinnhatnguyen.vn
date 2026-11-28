<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\ErpOrder;
use App\Models\ErpCustomer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ErpIntegrityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $models = ['ErpOrder','ErpOrderItem','ErpOrderPayment','ErpCustomer','ErpSupplier','ErpSupplierPayment','ErpCashbookEntry'];
        foreach ($models as $name) {
            $class = 'App\\Models\\'.$name;
            $model = new $class;
            Schema::create($model->getTable(), function(Blueprint $t) use ($model) {
                $t->id();
                foreach ($model->getFillable() as $field) {
                    if (str_ends_with($field,'_id') || $field === 'created_by') $t->unsignedBigInteger($field)->nullable();
                    elseif (in_array($model->getCasts()[$field] ?? '', ['float','double','integer'])) $t->decimal($field,18,2)->default(0)->nullable();
                    else $t->text($field)->nullable();
                }
                $t->timestamps();
            });
        }
        $this->staff(['orders.create'=>true,'orders.edit'=>true,'orders.payment'=>true,'orders.stage'=>true,'orders.delete'=>true,'clients.manage'=>true,'suppliers.manage'=>true,'cashbook.create'=>true,'cashbook.view'=>true]);
    }
    private function staff(array $permissions): void
    {
        $u=new User(['is_active'=>true,'custom_permissions'=>$permissions]);
        $u->id=1; $u->setRelation('role',null); Sanctum::actingAs($u);
    }
    private function order(array $extra=[]): ErpOrder
    {
        $r=$this->postJson('/api/v1/admin/erp/orders',array_replace([
            'customer_name'=>'Fixture','customer_phone'=>'0900000000','vat_rate'=>10,
            'shipping_fee_customer'=>30000,'shipping_fee_shop'=>40000,'discount'=>50000,
            'items'=>[['product_name'=>'Print','quantity'=>100,'unit_price'=>10000,'cost_price'=>6000]]
        ],$extra))->assertCreated();
        return ErpOrder::findOrFail($r->json('data.id'));
    }
    public function test_financial_formula_and_sequential_payments(): void
    {
        $o=$this->order(['paid_amount'=>300000]);
        $this->assertEquals(1130000,$o->total_amount);
        $this->assertEquals(340000,$o->profit_amount);
        $this->assertEquals(100000,$o->vat_amount);
        $this->postJson('/api/v1/admin/erp/orders/'.$o->id.'/payments',['amount'=>830000])->assertOk();
        $this->assertEquals(1130000,$o->fresh()->paid_amount);
        $this->assertSame('paid',$o->fresh()->payment_status);
        $this->assertEquals(1130000,$o->payments()->sum('amount'));
    }
    public function test_invalid_item_update_does_not_mutate_order(): void
    {
        $o=$this->order();
        $this->putJson('/api/v1/admin/erp/orders/'.$o->id,['items'=>[['product_name'=>'Bad','quantity'=>-1,'unit_price'=>100]]])->assertUnprocessable();
        $this->assertEquals(1130000,$o->fresh()->total_amount);
        $this->assertSame(1,$o->items()->count());
    }
    public function test_payment_failure_rolls_back(): void
    {
        $o=$this->order();
        ErpOrder::updating(function(){throw new \RuntimeException('fixture failure');});
        try {
            $this->postJson('/api/v1/admin/erp/orders/'.$o->id.'/payments',['amount'=>100])->assertStatus(500);
            $this->assertSame(0,$o->payments()->count());
            $this->assertEquals(0,$o->fresh()->paid_amount);
        } finally { ErpOrder::flushEventListeners(); }
    }
    public function test_payment_permission_is_independent(): void
    {
        $o=$this->order();
        $this->staff(['orders.edit'=>true]);
        $this->postJson('/api/v1/admin/erp/orders/'.$o->id.'/payments',['amount'=>100])->assertForbidden();
        $this->assertSame(0,$o->payments()->count());
    }
    public function test_target_stage_permission_cannot_be_bypassed(): void
    {
        $o=$this->order();
        $this->staff(['orders.edit'=>true]);
        $this->putJson('/api/v1/admin/erp/orders/'.$o->id,['production_stage'=>'hoan_thanh'])->assertForbidden();
    }
    public function test_target_reopened_order_status_is_consistent(): void
    {
        $o=$this->order();
        $url='/api/v1/admin/erp/orders/'.$o->id.'/stage';
        $this->postJson($url,['stage'=>'hoan_thanh'])->assertOk();
        $this->postJson($url,['stage'=>'dang_in'])->assertOk();
        $this->assertSame('in_progress',$o->fresh()->status);
    }
    public function test_target_import_preserves_commission(): void
    {
        $c=ErpCustomer::create(['name'=>'Fixture','phone'=>'0900000000','commission_rate'=>12]);
        $this->postJson('/api/v1/admin/erp/customers/import',['customers'=>[['name'=>'Updated','phone'=>$c->phone]]])->assertOk();
        $this->assertEquals(12,$c->fresh()->commission_rate);
    }
    public function test_target_import_rejects_invalid_commission(): void
    {
        $this->postJson('/api/v1/admin/erp/customers/import',['customers'=>[['name'=>'Fixture','phone'=>'0900000000','commission_rate'=>101]]])->assertUnprocessable();
        $this->assertDatabaseCount('erp_customers',0);
    }
    public function test_target_paid_order_cannot_be_deleted(): void
    {
        $o=$this->order(['paid_amount'=>100]);
        $this->deleteJson('/api/v1/admin/erp/orders/'.$o->id)->assertStatus(409);
        $this->assertDatabaseHas('erp_orders',['id'=>$o->id]);
    }
    public function test_target_linked_supplier_payment_survives_order_edit(): void
    {
        $o=$this->order();
        $item=$o->items()->firstOrFail();
        DB::table('erp_supplier_payments')->insert(['supplier_id'=>1,'order_item_id'=>$item->id,'amount'=>100]);
        $r=$this->putJson('/api/v1/admin/erp/orders/'.$o->id,['items'=>[['id'=>$item->id,'product_name'=>'Print updated','quantity'=>100,'unit_price'=>10000,'cost_price'=>6000]]]);
        $this->assertContains($r->status(),[200,409]);
        $this->assertDatabaseHas('erp_order_items',['id'=>$item->id]);
        $this->assertDatabaseHas('erp_supplier_payments',['order_item_id'=>$item->id,'amount'=>100]);
    }
    public function test_target_cashbook_rejects_missing_order(): void
    {
        $this->postJson('/api/v1/admin/erp/cashbook',['type'=>'receipt','category'=>'other','amount'=>100,'payment_method'=>'cash','order_id'=>999,'description'=>'Fixture','entry_date'=>'2026-11-28'])->assertUnprocessable();
    }
}
