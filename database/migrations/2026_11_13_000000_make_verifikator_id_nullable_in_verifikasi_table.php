<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('verifikasi') && Schema::hasColumn('verifikasi', 'verifikator_id')) {
            DB::statement("ALTER TABLE verifikasi MODIFY COLUMN verifikator_id BIGINT UNSIGNED NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('verifikasi') && Schema::hasColumn('verifikasi', 'verifikator_id')) {
            DB::statement("ALTER TABLE verifikasi MODIFY COLUMN verifikator_id BIGINT UNSIGNED NOT NULL");
        }
    }
};
