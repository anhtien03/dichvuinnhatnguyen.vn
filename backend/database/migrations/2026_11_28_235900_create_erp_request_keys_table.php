<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('erp_request_keys', function (Blueprint $table) {
            $table->string('scope', 64)->primary();
            $table->string('fingerprint', 64);
            $table->unsignedSmallInteger('status')->nullable();
            $table->longText('response')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_request_keys');
    }
};
