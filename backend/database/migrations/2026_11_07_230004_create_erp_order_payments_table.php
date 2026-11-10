<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('erp_orders')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('bank_acb'); // bank_acb, bank_mb, cash, other
            $table->string('funding_owner')->default('company'); // company, truong_hung, minh_nguyen
            $table->text('note')->nullable();
            $table->dateTime('paid_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_order_payments');
    }
};

