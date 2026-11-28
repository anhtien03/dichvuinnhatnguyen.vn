<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
final class ErpCodeSequence
{
    public static function next(string $table, string $prefix, bool $reserve = true): string
    {
        if ($reserve) {
            if (DB::transactionLevel() === 0) throw new \LogicException('Code allocation requires a transaction.');
            DB::table('erp_code_sequences')->insertOrIgnore(['prefix'=>$prefix,'last_number'=>0]);
        }
        $query = DB::table('erp_code_sequences')->where('prefix',$prefix);
        $last = (int) ($reserve ? $query->lockForUpdate()->value('last_number') : $query->value('last_number'));
        $codes = DB::table($table)->where('code','like',$prefix.'%');
        if ($reserve) $codes->lockForUpdate();
        foreach ($codes->pluck('code') as $code) {
            if (preg_match('/-(\\d+)$/',$code,$m)) $last = max($last,(int)$m[1]);
        }
        $next = $last + 1;
        if ($reserve) DB::table('erp_code_sequences')->where('prefix',$prefix)->update(['last_number'=>$next]);
        return $prefix.str_pad((string)$next,3,'0',STR_PAD_LEFT);
    }
}
