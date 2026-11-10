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
        Schema::table('banners', function (Blueprint $table) {
            $table->text('image_url')->nullable()->change();
            $table->string('badge')->nullable()->after('subtitle');
            $table->string('extra_text')->nullable()->after('button_text');
            $table->string('bg_color')->nullable()->default('blue')->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['badge', 'extra_text', 'bg_color']);
        });
    }
};
