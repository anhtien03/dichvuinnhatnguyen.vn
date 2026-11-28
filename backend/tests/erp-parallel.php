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
        $user=new \App\Models\User(['is_active'=>true,'custom_permissions'=>['orders.create'=>true,'orders.edit'=>true,'orders.delete'=>true,'orders.payment'=>true,'cashbook.create'=>true]]);
        $user->id=1; $user->setRelation('role',null);
        if ($mode==='http-denied') $user->custom_permissions=[];
        if ($mode==='http-fault') \App\Models\ErpOrder::updating(function(){ throw new RuntimeException('Injected payment failure'); });
        \Laravel\Sanctum\Sanctum::actingAs($user);
        $path='/api/v1/admin/erp/cashbook';
        $payload=['type'=>$mode==='http-expense'?'payment':'receipt','category'=>'other','amount'=>100,'payment_method'=>'cash','description'=>'Fixture','entry_date'=>'2026-11-28'];
        if ($mode==='http-order') {
            $path='/api/v1/admin/erp/orders';
            $payload=['customer_name'=>'Fixture','customer_phone'=>'090000'.str_pad((string)$i,4,'0',STR_PAD_LEFT),'items'=>[['product_name'=>'Print','quantity'=>100,'unit_price'=>100]]];
        } elseif (in_array($mode,['http-replay','http-distinct','http-fault','http-retry'],true)) {
            $path='/api/v1/admin/erp/orders/1/payments'; $payload=['amount'=>100];
        }
        $method='POST';
        if ($mode==='http-race-edit') { $path='/api/v1/admin/erp/orders/2'; $method='PUT'; $payload=['notes'=>'Race edit','shipping_fee_customer'=>500]; }
        if ($mode==='http-race-pay') { $path='/api/v1/admin/erp/orders/2/payments'; $payload=['amount'=>100]; }
        if ($mode==='http-race-delete') { $path='/api/v1/admin/erp/orders/'.(3+$i); $method='DELETE'; $payload=[]; }
        if ($mode==='http-race-delete-pay') { $path='/api/v1/admin/erp/orders/'.(3+$i).'/payments'; $payload=['amount'=>100]; }
        if ($mode==='http-create-fault') { $path='/api/v1/admin/erp/orders'; $payload=['customer_name'=>'Fault fixture','customer_phone'=>'0999999999','order_date'=>'2025-01-01','items'=>[['product_name'=>'Print','quantity'=>100,'unit_price'=>100]]]; \App\Models\ErpOrder::creating(function(){throw new RuntimeException('Injected create failure');}); }
        $request=Request::create($path,$method,[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_ACCEPT'=>'application/json','HTTP_IDEMPOTENCY_KEY'=>$mode==='http-replay'?'shared-http':($mode==='http-cash-replay'?'shared-cash':(in_array($mode,['http-fault','http-retry'],true)?'fault-retry':'http-'.$mode.'-'.$i))],json_encode($payload));
        $response=app(\Illuminate\Contracts\Http\Kernel::class)->handle($request);
        if ($mode==='http-race-delete' && $response->getStatusCode()===409) exit(0);
        if ($mode==='http-race-delete-pay' && $response->getStatusCode()===404) exit(0);
        check($response->getStatusCode()===match($mode){'http-denied'=>403,'http-fault','http-create-fault'=>500,'http-race-edit','http-race-pay','http-race-delete','http-race-delete-pay'=>200,'http-replay','http-distinct','http-retry'=>200,default=>201},$mode.' HTTP '.$response->getStatusCode().' '.$response->getContent());
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

function runExtraWorkers(string $mode,int $count): void {
    $workers=[];
    for($i=0;$i<$count;$i++){
        $p=proc_open([PHP_BINARY,__FILE__,'worker',$mode,(string)$i],[0=>['file','/dev/null','r'],1=>STDOUT,2=>STDERR],$pipes);
        check(is_resource($p),'Worker launch failed'); $workers[]=$p;
    }
    $failed=0; foreach($workers as $p) if(proc_close($p)!==0) $failed++;
    check($failed===0,"$mode failures: $failed");
}
$cashBefore=DB::table('erp_cashbook_entries')->count();
runExtraWorkers('http-denied',20);
check(DB::table('erp_cashbook_entries')->count()===$cashBefore,'Denied requests mutated cashbook');
runExtraWorkers('http-cash-replay',20);
check(DB::table('erp_cashbook_entries')->count()===$cashBefore+1,'Cash replay count mismatch');
check((int)DB::table('erp_cashbook_entries')->sum('amount')===4100,'Cash replay sum mismatch');
runExtraWorkers('http-fault',1);
check(DB::table('erp_order_payments')->where('order_id',1)->count()===21,'Fault leaked payment');
check((int)DB::table('erp_orders')->where('id',1)->value('paid_amount')===2100,'Fault changed paid amount');
runExtraWorkers('http-retry',1);
check(DB::table('erp_order_payments')->where('order_id',1)->count()===22,'Retry payment count mismatch');
check((int)DB::table('erp_orders')->where('id',1)->value('paid_amount')===2200,'Retry paid amount mismatch');
DB::table('erp_ci_records')->insert(['code'=>'NN-02012026-999','amount'=>100]);
check(DB::transaction(fn()=>ErpCodeSequence::next('erp_ci_records','NN-02012026-'))==='NN-02012026-1000','Sequence overflow at 999');
echo "HTTP denial, cash replay, payment fault/retry and sequence overflow: PASS\n";

$failures=[];
function diagnostic(string $name, callable $fn): void {
    global $failures;
    try { $fn(); echo "$name: PASS\n"; }
    catch (Throwable $e) { $failures[]=$name; echo "$name: FAIL ".$e->getMessage()."\n"; }
}
function mixedWorkers(array $modes,int $count): void {
    $workers=[];
    for($i=0;$i<$count;$i++) foreach($modes as $mode) {
        $p=proc_open([PHP_BINARY,__FILE__,'worker',$mode,(string)$i],[0=>['file','/dev/null','r'],1=>STDOUT,2=>STDERR],$pipes);
        check(is_resource($p),'Worker launch failed'); $workers[]=$p;
    }
    $failed=0; foreach($workers as $p) if(proc_close($p)!==0) $failed++;
    check($failed===0,"Mixed worker failures: $failed");
}
diagnostic('E5 edit/payment race',function(){
    mixedWorkers(['http-race-edit','http-race-pay'],10);
    $o=DB::table('erp_orders')->where('id',2)->first();
    $q=DB::table('erp_order_payments')->where('order_id',2);
    check($q->count()===10 && (float)$q->sum('amount')===1000.0,'Payment rows mismatch');
    check((float)$o->paid_amount===1000.0 && (float)$o->total_amount===10500.0 && $o->payment_status==='partial','Order financial state mismatch');
});
diagnostic('E5 delete/payment race',function(){
    mixedWorkers(['http-race-delete','http-race-delete-pay'],10);
    for($id=3;$id<13;$id++){
        $o=DB::table('erp_orders')->where('id',$id)->first();
        $q=DB::table('erp_order_payments')->where('order_id',$id);
        check($o ? ($q->count()===1 && (float)$o->paid_amount===100.0) : $q->count()===0,"Invalid final state for order $id");
    }
});
diagnostic('E5 create fault rollback',function(){
    $before=DB::table('erp_orders')->count();
    $seq=DB::table('erp_code_sequences')->where('prefix','NN-01012025-')->first();
    runExtraWorkers('http-create-fault',1);
    check(DB::table('erp_orders')->count()===$before,'Order leaked');
    check(DB::table('erp_code_sequences')->where('prefix','NN-01012025-')->first()==$seq,'Sequence leaked');
    check(!DB::table('erp_customers')->where('phone','0999999999')->exists(),'Customer leaked');
});
check(!$failures,'E5 failed groups: '.implode(', ',$failures));
