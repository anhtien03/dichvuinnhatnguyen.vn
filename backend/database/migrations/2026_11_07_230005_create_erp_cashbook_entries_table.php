<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_cashbook_entries', function (Blueprint $table) {
            $table->id();
            $table->string('code')->index(); // PT-xxx hoặc PC-xxx
            $table->string('type'); // receipt (thu) hoặc payment (chi)
            $table->string('category'); // shipping, materials, commission, office, meal, advance, other
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('cash'); // cash, bank_acb, bank_mb, other
            $table->foreignId('order_id')->nullable()->constrained('erp_orders')->nullOnDelete(); // Gắn vào đơn để khấu trừ tiền lời
            $table->string('funding_owner')->default('company'); // company, truong_hung, minh_nguyen
            $table->string('payer_recipient')->nullable();
            $table->text('description');
            $table->date('entry_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_cashbook_entries');
    }
};

