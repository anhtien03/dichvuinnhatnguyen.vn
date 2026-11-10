<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index(); // NN-DDMMYYYY-XXX
            $table->foreignId('customer_id')->nullable()->constrained('erp_customers')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->index();
            $table->text('customer_address')->nullable();
            $table->string('customer_company')->nullable();
            $table->string('customer_tax_code')->nullable();

            // Production & Stage
            $table->string('production_stage')->default('cho_duyet_file'); // cho_duyet_file, thiet_ke, gui_file_in, dang_in, gia_cong, da_in_xong, dang_giao, hoan_thanh
            $table->string('status')->default('in_progress'); // in_progress, completed, cancelled
            $table->string('printer_name')->nullable();
            $table->string('followers')->nullable();

            // Financial Calculations
            $table->decimal('subtotal', 15, 2)->default(0); // Tiền hàng trước chiết khấu
            $table->decimal('discount', 15, 2)->default(0); // Giảm giá
            $table->decimal('vat_rate', 5, 2)->default(0); // 8% / 10%
            $table->decimal('vat_amount', 15, 2)->default(0); // Thuế VAT bán
            $table->decimal('shipping_fee_customer', 15, 2)->default(0); // Ship khách trả
            $table->decimal('shipping_fee_shop', 15, 2)->default(0); // Ship xưởng
            $table->decimal('surcharge', 15, 2)->default(0); // Phụ thu
            $table->decimal('total_amount', 15, 2)->default(0); // Tổng thanh toán = subtotal - discount + vat + ship_customer + surcharge
            $table->decimal('cost_amount', 15, 2)->default(0); // Tổng vốn xưởng
            $table->decimal('profit_amount', 15, 2)->default(0); // Lợi nhuận gộp thực tế
            $table->decimal('paid_amount', 15, 2)->default(0); // Đã thanh toán / cọc
            $table->string('payment_status')->default('unpaid'); // unpaid, partial, paid

            // VAT Invoice Tracking
            $table->boolean('is_vat_required')->default(false);
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('invoice_status')->default('none'); // none, pending, issued

            // Delete request workflow
            $table->boolean('delete_requested')->default(false);
            $table->text('delete_reason')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_orders');
    }
};

