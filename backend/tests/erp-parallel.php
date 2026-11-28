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
    if ($mode === 'sequence') {
        DB::transaction(function() {
            $code=ErpCodeSequence::next('erp_ci_records','NN-01012026-');
            DB::table('erp_ci_records')->insert(['code'=>$code,'amount'=>100]);
        },5);
    } else {
        $request=Request::create('/fixture','POST');
        $request->setUserResolver(fn()=>(object)['id'=>1]);
        $request->headers->set('Idempotency-Key',$mode==='replay'?'shared-key':'key-'.$i);
        ErpIdempotency::run($request,$mode,['amount'=>100],function(){
            DB::table('erp_ci_records')->insert(['amount'=>100]);
            return response()->json(['ok'=>true]);
        });
    }
    exit(0);
}
(require database_path('migrations/2026_11_29_000002_create_erp_code_sequences.php'))->up();
(require database_path('migrations/2026_11_28_235900_create_erp_request_keys_table.php'))->up();
Schema::create('erp_ci_records',function(Blueprint $t){$t->id();$t->string('code')->nullable()->unique();$t->integer('amount');});
foreach (['sequence'=>20,'replay'=>1,'distinct'=>20] as $mode=>$expected) {
    DB::table('erp_ci_records')->delete();
    $workers=[];
    for($i=0;$i<20;$i++){
        $p=proc_open([PHP_BINARY,__FILE__,'worker',$mode,(string)$i],[0=>['file','/dev/null','r'],1=>STDOUT,2=>STDERR],$pipes);
        check(is_resource($p),'Worker launch failed');
        $workers[]=$p;
    }
    $failed=0;
    foreach($workers as $p) if(proc_close($p)!==0) $failed++;
    check($failed===0,"$mode worker failures: $failed");
    check(DB::table('erp_ci_records')->count()===$expected,"$mode count mismatch");
    check((int)DB::table('erp_ci_records')->sum('amount')===$expected*100,"$mode amount mismatch");
    echo "$mode: PASS (20 workers, $expected records)\n";
}
