<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('erp_orders')->cascadeOnDelete();
            $table->string('product_name');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit')->default('Cái');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);

            // Item discount
            $table->string('discount_type')->default('none'); // none, fixed, percent
            $table->decimal('discount_value', 15, 2)->default(0);

            // Cost & Supplier
            $table->foreignId('supplier_id')->nullable()->constrained('erp_suppliers')->nullOnDelete();
            $table->string('supplier_name')->nullable();
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('cost_total', 15, 2)->default(0);

            // Input VAT
            $table->decimal('vat_input_rate', 5, 2)->default(0);
            $table->decimal('vat_input_amount', 15, 2)->default(0);
            $table->boolean('vat_input_deductible')->default(false);

            $table->text('specifications')->nullable(); // Kích thước, loại giấy, cán màng, bế...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_order_items');
    }
};

