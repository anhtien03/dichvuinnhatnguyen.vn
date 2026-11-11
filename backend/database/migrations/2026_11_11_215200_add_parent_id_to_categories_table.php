<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'parent_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('categories')
                    ->nullOnDelete();
            });
        }

        // Tự động gắn các danh mục con đã tạo (như "in-the-danh-thiep") vào nhóm ngành gốc tương ứng
        $rootSlugs = [
            'an-pham-van-phong',
            'tiep-thi-quang-cao',
            'tem-nhan-decal',
            'bao-bi-tui-giay',
            'su-kien-trien-lam',
            'lich-so-tay',
        ];

        $vanPhongId = DB::table('categories')->where('slug', 'an-pham-van-phong')->value('id');
        if ($vanPhongId) {
            DB::table('categories')
                ->whereNotIn('slug', $rootSlugs)
                ->whereNull('parent_id')
                ->update(['parent_id' => $vanPhongId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'parent_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }
    }
};

