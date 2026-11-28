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
        (require database_path('migrations/2026_11_28_235900_create_erp_request_keys_table.php'))->up();
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

    private function cashPayload(array $extra=[]): array
    {
        return array_replace(['type'=>'receipt','category'=>'other','amount'=>100,'payment_method'=>'cash','description'=>'Fixture','entry_date'=>'2026-11-28'],$extra);
    }
    public function test_concurrency_order_codes_distinct_sequentially(): void
    {
        $a=$this->order(); $b=$this->order();
        $this->assertNotSame($a->code,$b->code);
    }
    public function test_concurrency_order_code_date_prefix(): void
    {
        $o=$this->order(['order_date'=>'2026-11-28']);
        $this->assertStringStartsWith('NN-28112026-',$o->code);
    }
    public function test_concurrency_order_code_unique_constraint(): void
    {
        $this->order();
        $indexes=Schema::getIndexes('erp_orders');
        $this->assertTrue(collect($indexes)->contains(fn($i)=>$i['unique'] && $i['columns']===['code']), 'Fixture has no unique code index; verify production migrations separately.');
    }
    public function test_concurrency_payment_replay(): void
    {
        $o=$this->order(); $url='/api/v1/admin/erp/orders/'.$o->id.'/payments';
        $h=['Idempotency-Key'=>'11111111-1111-4111-8111-111111111111'];
        $this->postJson($url,['amount'=>100],$h)->assertSuccessful();
        $this->postJson($url,['amount'=>100],$h)->assertSuccessful();
        $this->assertSame(1,$o->payments()->count());
        $this->assertEquals(100,$o->fresh()->paid_amount);
    }
    public function test_concurrency_payment_key_conflict(): void
    {
        $o=$this->order(); $url='/api/v1/admin/erp/orders/'.$o->id.'/payments';
        $h=['Idempotency-Key'=>'11111111-1111-4111-8111-111111111111'];
        $this->postJson($url,['amount'=>100],$h)->assertSuccessful();
        $this->postJson($url,['amount'=>200],$h)->assertStatus(409);
        $this->assertEquals(100,$o->fresh()->paid_amount);
    }
    public function test_concurrency_payment_distinct_keys_same_amount(): void
    {
        $o=$this->order(); $url='/api/v1/admin/erp/orders/'.$o->id.'/payments';
        foreach (['11111111-1111-4111-8111-111111111111','22222222-2222-4222-8222-222222222222'] as $key)
            $this->postJson($url,['amount'=>100],['Idempotency-Key'=>$key])->assertSuccessful();
        $this->assertSame(2,$o->payments()->count());
        $this->assertEquals(200,$o->fresh()->paid_amount);
    }
    public function test_concurrency_payment_invalid_key(): void
    {
        $o=$this->order();
        $this->postJson('/api/v1/admin/erp/orders/'.$o->id.'/payments',['amount'=>100],['Idempotency-Key'=>str_repeat('x',300)])->assertUnprocessable();
        $this->assertSame(0,$o->payments()->count());
    }
    public function test_concurrency_customer_duplicate_create(): void
    {
        $p=['name'=>'Fixture','phone'=>'0900000000'];
        $this->postJson('/api/v1/admin/erp/customers',$p)->assertCreated();
        $this->postJson('/api/v1/admin/erp/customers',$p)->assertUnprocessable();
        $this->assertDatabaseCount('erp_customers',1);
    }
    public function test_concurrency_customer_duplicate_update(): void
    {
        $a=ErpCustomer::create(['name'=>'A','phone'=>'0900000000']);
        $b=ErpCustomer::create(['name'=>'B','phone'=>'0900000001']);
        $this->putJson('/api/v1/admin/erp/customers/'.$b->id,['phone'=>$a->phone])->assertUnprocessable();
        $this->assertSame('0900000001',$b->fresh()->phone);
    }
    public function test_concurrency_customer_order_reuses_phone(): void
    {
        $a=$this->order(); $b=$this->order();
        $this->assertEquals($a->customer_id,$b->customer_id);
        $this->assertDatabaseCount('erp_customers',1);
    }
    public function test_concurrency_cash_codes_distinct_sequentially(): void
    {
        $url='/api/v1/admin/erp/cashbook';
        $a=$this->postJson($url,$this->cashPayload())->assertCreated();
        $b=$this->postJson($url,$this->cashPayload())->assertCreated();
        $this->assertNotSame($a->json('data.code'),$b->json('data.code'));
    }
    public function test_concurrency_cash_code_prefixes(): void
    {
        $url='/api/v1/admin/erp/cashbook';
        foreach (['receipt'=>'PT-','payment'=>'PC-'] as $type=>$prefix)
            $this->assertStringStartsWith($prefix,$this->postJson($url,$this->cashPayload(['type'=>$type]))->assertCreated()->json('data.code'));
    }
    public function test_concurrency_cash_replay(): void
    {
        $url='/api/v1/admin/erp/cashbook'; $h=['Idempotency-Key'=>'11111111-1111-4111-8111-111111111111'];
        $this->postJson($url,$this->cashPayload(),$h)->assertSuccessful();
        $this->postJson($url,$this->cashPayload(),$h)->assertSuccessful();
        $this->assertDatabaseCount('erp_cashbook_entries',1);
    }
    public function test_concurrency_cash_key_conflict(): void
    {
        $url='/api/v1/admin/erp/cashbook'; $h=['Idempotency-Key'=>'11111111-1111-4111-8111-111111111111'];
        $this->postJson($url,$this->cashPayload(),$h)->assertSuccessful();
        $this->postJson($url,$this->cashPayload(['amount'=>200]),$h)->assertStatus(409);
        $this->assertDatabaseCount('erp_cashbook_entries',1);
    }
    public function test_concurrency_cash_distinct_keys_same_amount(): void
    {
        foreach (['11111111-1111-4111-8111-111111111111','22222222-2222-4222-8222-222222222222'] as $key)
            $this->postJson('/api/v1/admin/erp/cashbook',$this->cashPayload(),['Idempotency-Key'=>$key])->assertSuccessful();
        $this->assertDatabaseCount('erp_cashbook_entries',2);
    }
    public function test_concurrency_import_accepts_per_order_commission(): void
    {
        $this->postJson('/api/v1/admin/erp/customers/import',['customers'=>[['name'=>'Fixture','phone'=>'0900000000','commission_type'=>'per_order']]])->assertOk();
        $this->assertDatabaseHas('erp_customers',['phone'=>'0900000000','commission_type'=>'per_order']);
    }
}
