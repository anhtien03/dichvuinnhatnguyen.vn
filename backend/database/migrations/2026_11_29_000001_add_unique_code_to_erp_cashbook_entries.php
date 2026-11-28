<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (DB::table('erp_cashbook_entries')->select('code')->groupBy('code')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new \RuntimeException('Duplicate cashbook codes exist. Reconcile records before migration.');
        }
        Schema::table('erp_cashbook_entries', function (Blueprint $table) {
            $table->unique('code', 'erp_cashbook_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('erp_cashbook_entries', function (Blueprint $table) {
            $table->dropUnique('erp_cashbook_code_unique');
        });
    }
};
