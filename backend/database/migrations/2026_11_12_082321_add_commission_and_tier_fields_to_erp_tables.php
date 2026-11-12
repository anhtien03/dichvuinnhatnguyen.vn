<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('erp_customers', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(0)->after('tag')->comment('Tỷ lệ chiết khấu/hoa hồng định kỳ (%)');
            $table->string('commission_type', 50)->default('monthly')->after('commission_rate')->comment('monthly: Chốt hàng tháng, per_order: Trừ ngay trên đơn');
        });

        Schema::table('erp_orders', function (Blueprint $table) {
            $table->string('discount_mode', 50)->default('direct')->after('discount')->comment('direct: Trừ trực tiếp đơn này, monthly_commission: Tích lũy tính hoa hồng tháng');
            $table->decimal('commission_rate_snapshot', 5, 2)->default(0)->after('discount_mode')->comment('Tỷ lệ hoa hồng ghi nhận tại thời điểm tạo đơn (%)');
            $table->decimal('commission_amount', 15, 2)->default(0)->after('commission_rate_snapshot')->comment('Số tiền hoa hồng phát sinh từ đơn này (₫)');
            $table->string('commission_status', 50)->default('pending')->after('commission_amount')->comment('pending: Chưa chốt, calculated: Đã tính kỳ, paid: Đã chi trả');
            $table->date('commission_period')->nullable()->after('commission_status')->comment('Kỳ tính hoa hồng (VD: 2026-11-01)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_customers', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_type']);
        });

        Schema::table('erp_orders', function (Blueprint $table) {
            $table->dropColumn([
                'discount_mode',
                'commission_rate_snapshot',
                'commission_amount',
                'commission_status',
                'commission_period',
            ]);
        });
    }
};
