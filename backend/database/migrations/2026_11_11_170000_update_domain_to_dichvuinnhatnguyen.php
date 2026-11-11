<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update canonical_url in products
        DB::statement("UPDATE products SET canonical_url = REPLACE(canonical_url, 'https://innhatnguyen.vn', 'https://dichvuinnhatnguyen.vn') WHERE canonical_url LIKE '%innhatnguyen.vn%'");
        DB::statement("UPDATE products SET canonical_url = REPLACE(canonical_url, 'http://innhatnguyen.vn', 'https://dichvuinnhatnguyen.vn') WHERE canonical_url LIKE '%innhatnguyen.vn%'");

        // Update canonical_url in categories
        DB::statement("UPDATE categories SET canonical_url = REPLACE(canonical_url, 'https://innhatnguyen.vn', 'https://dichvuinnhatnguyen.vn') WHERE canonical_url LIKE '%innhatnguyen.vn%'");
        DB::statement("UPDATE categories SET canonical_url = REPLACE(canonical_url, 'http://innhatnguyen.vn', 'https://dichvuinnhatnguyen.vn') WHERE canonical_url LIKE '%innhatnguyen.vn%'");

        // Update canonical_url in posts if column exists
        if (\Illuminate\Support\Facades\Schema::hasColumn('posts', 'canonical_url')) {
            DB::statement("UPDATE posts SET canonical_url = REPLACE(canonical_url, 'https://innhatnguyen.vn', 'https://dichvuinnhatnguyen.vn') WHERE canonical_url LIKE '%innhatnguyen.vn%'");
        }

        // Update site_settings
        DB::statement("UPDATE site_settings SET value = REPLACE(value, 'https://innhatnguyen.vn', 'https://dichvuinnhatnguyen.vn') WHERE value LIKE '%innhatnguyen.vn%'");
        DB::statement("UPDATE site_settings SET value = REPLACE(value, 'innhatnguyen.vn', 'dichvuinnhatnguyen.vn') WHERE `key` = 'domain' OR `key` = 'site_url'");
    }

    public function down(): void
    {
        DB::statement("UPDATE products SET canonical_url = REPLACE(canonical_url, 'https://dichvuinnhatnguyen.vn', 'https://innhatnguyen.vn') WHERE canonical_url LIKE '%dichvuinnhatnguyen.vn%'");
        DB::statement("UPDATE categories SET canonical_url = REPLACE(canonical_url, 'https://dichvuinnhatnguyen.vn', 'https://innhatnguyen.vn') WHERE canonical_url LIKE '%dichvuinnhatnguyen.vn%'");
    }
};
