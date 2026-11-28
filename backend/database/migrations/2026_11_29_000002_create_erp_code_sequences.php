<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('erp_code_sequences', function (Blueprint $table) {
            $table->string('prefix',32)->primary();
            $table->unsignedBigInteger('last_number')->default(0);
        });
    }
    public function down(): void { Schema::dropIfExists('erp_code_sequences'); }
};
