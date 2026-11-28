<?php
// Standalone CI-only service-level concurrency harness.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Services\ErpCodeSequence;
use App\Services\ErpIdempotency;
use Illuminate\Http\Request;
if (!app()->environment('testing') || DB::connection()->getDriverName() !== 'mysql' || DB::connection()->getDatabaseName() !== 'erp_ci_only' || getenv('GITHUB_ACTIONS') !== 'true') {
    throw new RuntimeException('CI-only database required');
}
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
if (($argv[1] ?? '') === 'worker') {
    $mode=$argv[2]; $i=(int)$argv[3];
    if (str_starts_with($mode,'http-')) {
        $user=new \App\Models\User(['is_active'=>true,'custom_permissions'=>['orders.create'=>true,'orders.payment'=>true,'cashbook.create'=>true]]);
        $user->id=1; $user->setRelation('role',null);
        \Laravel\Sanctum\Sanctum::actingAs($user);
        $path='/api/v1/admin/erp/cashbook';
        $payload=['type'=>$mode==='http-expense'?'payment':'receipt','category'=>'other','amount'=>100,'payment_method'=>'cash','description'=>'Fixture','entry_date'=>'2026-11-28'];
        if ($mode==='http-order') {
            $path='/api/v1/admin/erp/orders';
            $payload=['customer_name'=>'Fixture','customer_phone'=>'090000'.str_pad((string)$i,4,'0',STR_PAD_LEFT),'items'=>[['product_name'=>'Print','quantity'=>100,'unit_price'=>100]]];
        } elseif (in_array($mode,['http-replay','http-distinct'],true)) {
            $path='/api/v1/admin/erp/orders/1/payments'; $payload=['amount'=>100];
        }
        $request=Request::create($path,'POST',[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_ACCEPT'=>'application/json','HTTP_IDEMPOTENCY_KEY'=>$mode==='http-replay'?'shared-http':'http-'.$mode.'-'.$i],json_encode($payload));
        $response=app(\Illuminate\Contracts\Http\Kernel::class)->handle($request);
        check($response->getStatusCode()===($mode==='http-replay'||$mode==='http-distinct'?200:201),$mode.' HTTP '.$response->getStatusCode().' '.$response->getContent());
        exit(0);
    }
    if (in_array($mode,['sequence','receipt','expense'],true)) {
        DB::transaction(function() use ($mode) {
            $prefix = ['sequence'=>'NN-01012026-','receipt'=>'PT-2601-','expense'=>'PC-2601-'][$mode];
            $code=ErpCodeSequence::next('erp_ci_records',$prefix);
            DB::table('erp_ci_records')->insert(['code'=>$code,'amount'=>100]);
        },5);
    } else {
        $request=Request::create('/fixture','POST');
        $request->setUserResolver(fn()=>(object)['id'=>1]);
        $request->headers->set('Idempotency-Key',in_array($mode,['replay','conflict'],true)?'shared-key':'key-'.$i);
        $amount = $mode==='conflict' ? 100+$i : 100;
        try {
            ErpIdempotency::run($request,$mode,['amount'=>$amount],function() use ($amount){
                DB::table('erp_ci_records')->insert(['amount'=>$amount]);
                return response()->json(['ok'=>true]);
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            if ($mode!=='conflict' || $e->getStatusCode()!==409) throw $e;
            exit(9);
        }
    }
    exit(0);
}
(require database_path('migrations/2026_11_29_000002_create_erp_code_sequences.php'))->up();
(require database_path('migrations/2026_11_28_235900_create_erp_request_keys_table.php'))->up();
Schema::create('erp_ci_records',function(Blueprint $t){$t->id();$t->string('code')->nullable()->unique();$t->integer('amount');});
foreach (['sequence'=>20,'receipt'=>20,'expense'=>20,'replay'=>1,'distinct'=>20,'conflict'=>1] as $mode=>$expected) {
    DB::table('erp_ci_records')->delete();
    $workers=[];
    for($i=0;$i<20;$i++){
        $p=proc_open([PHP_BINARY,__FILE__,'worker',$mode,(string)$i],[0=>['file','/dev/null','r'],1=>STDOUT,2=>STDERR],$pipes);
        check(is_resource($p),'Worker launch failed');
        $workers[]=$p;
    }
    $failed=0; $conflicts=0;
    foreach($workers as $p) { $status=proc_close($p); if($mode==='conflict' && $status===9) $conflicts++; elseif($status!==0) $failed++; }
    if($mode==='conflict') check($conflicts===19,'Expected 19 conflicts');
    check($failed===0,"$mode worker failures: $failed");
    check(DB::table('erp_ci_records')->count()===$expected,"$mode count mismatch");
    $sum=(int)DB::table('erp_ci_records')->sum('amount');
    check($mode==='conflict' ? ($sum>=100 && $sum<=119) : $sum===$expected*100,"$mode amount mismatch");
    echo "$mode: PASS (20 workers, $expected records)\n";
}

DB::table('erp_ci_records')->delete();
$request=Request::create('/fixture','POST');
$request->setUserResolver(fn()=>(object)['id'=>1]);
$request->headers->set('Idempotency-Key','rollback-key');
try {
    ErpIdempotency::run($request,'rollback',['amount'=>100],function(){
        DB::table('erp_ci_records')->insert(['amount'=>100]);
        throw new RuntimeException('injected failure');
    });
    throw new LogicException('Expected injected failure');
} catch (RuntimeException $e) { check($e->getMessage()==='injected failure','Unexpected failure'); }
check(DB::table('erp_ci_records')->count()===0,'Rollback leaked record');
ErpIdempotency::run($request,'rollback',['amount'=>100],function(){
    DB::table('erp_ci_records')->insert(['amount'=>100]);
    return response()->json(['ok'=>true]);
});
check(DB::table('erp_ci_records')->count()===1,'Retry after rollback failed');
echo "rollback and retry: PASS\n";

foreach (['ErpOrder','ErpOrderItem','ErpOrderPayment','ErpCustomer','ErpSupplier','ErpSupplierPayment','ErpCashbookEntry'] as $name) {
    $class='App\\Models\\'.$name; $model=new $class;
    Schema::create($model->getTable(),function(Blueprint $t) use ($model){
        $t->id();
        foreach($model->getFillable() as $field){
            if($field==='code') $t->string('code')->nullable();
            elseif(str_ends_with($field,'_id')||$field==='created_by') $t->unsignedBigInteger($field)->nullable();
            elseif(in_array($model->getCasts()[$field]??'',['float','double','integer'])) $t->decimal($field,18,2)->default(0)->nullable();
            else $t->text($field)->nullable();
        }
        if(in_array($model->getTable(),['erp_orders','erp_cashbook_entries'])) $t->unique('code');
        $t->timestamps();
    });
}
foreach(['http-order','http-receipt','http-expense','http-replay','http-distinct'] as $mode){
    $workers=[];
    for($i=0;$i<20;$i++){
        $p=proc_open([PHP_BINARY,__FILE__,'worker',$mode,(string)$i],[0=>['file','/dev/null','r'],1=>STDOUT,2=>STDERR],$pipes);
        check(is_resource($p),'HTTP worker launch failed'); $workers[]=$p;
    }
    $failed=0; foreach($workers as $p) if(proc_close($p)!==0) $failed++;
    check($failed===0,"$mode failures: $failed");
    if($mode==='http-order') check(DB::table('erp_orders')->count()===20,'Order count mismatch');
    elseif($mode==='http-receipt'||$mode==='http-expense') {
        $q=DB::table('erp_cashbook_entries')->where('type',$mode==='http-receipt'?'receipt':'payment');
        check($q->count()===20 && (int)$q->sum('amount')===2000,'Cash count or sum mismatch');
    } else {
        $expected=$mode==='http-replay'?1:21;
        $q=DB::table('erp_order_payments')->where('order_id',1);
        check($q->count()===$expected && (int)$q->sum('amount')===$expected*100,'Payment count or sum mismatch');
        check((int)DB::table('erp_orders')->where('id',1)->value('paid_amount')===$expected*100,'Order paid amount mismatch');
    }
    echo "$mode: PASS (20 workers)\n";
}
