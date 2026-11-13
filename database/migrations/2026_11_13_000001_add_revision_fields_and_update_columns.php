<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Data Pribadi: anak_ke dan jumlah_saudara
        if (Schema::hasTable('data_pribadi')) {
            Schema::table('data_pribadi', function (Blueprint $table) {
                if (!Schema::hasColumn('data_pribadi', 'anak_ke')) {
                    $table->integer('anak_ke')->nullable()->after('status_dalam_keluarga');
                }
                if (!Schema::hasColumn('data_pribadi', 'jumlah_saudara')) {
                    $table->integer('jumlah_saudara')->nullable()->after('anak_ke');
                }
            });
        }

        // 2. Data Orang Tua: ubah enum pekerjaan_ayah dan pekerjaan_ibu jadi VARCHAR(100)
        if (Schema::hasTable('data_orang_tua')) {
            if (Schema::hasColumn('data_orang_tua', 'pekerjaan_ayah')) {
                DB::statement("ALTER TABLE data_orang_tua MODIFY COLUMN pekerjaan_ayah VARCHAR(100) NULL");
            }
            if (Schema::hasColumn('data_orang_tua', 'pekerjaan_ibu')) {
                DB::statement("ALTER TABLE data_orang_tua MODIFY COLUMN pekerjaan_ibu VARCHAR(100) NULL");
            }
        }

        // 3. Organisasi: tambah periode_jabatan
        if (Schema::hasTable('organisasi')) {
            Schema::table('organisasi', function (Blueprint $table) {
                if (!Schema::hasColumn('organisasi', 'periode_jabatan')) {
                    $table->string('periode_jabatan', 100)->nullable()->after('jabatan');
                }
            });
        }

        // 4. Users: Jadikan nik & nisn nullable untuk akun non-pendaftar (admin/verifikator)
        // dan bebaskan NISN dummy seeder (1234567890 / 1234567891) agar pendaftar bisa menggunakannya
        if (Schema::hasTable('users')) {
            try {
                DB::statement("ALTER TABLE users MODIFY COLUMN nik VARCHAR(16) NULL");
                DB::statement("ALTER TABLE users MODIFY COLUMN nisn VARCHAR(100) NULL");
                DB::statement("UPDATE users SET nisn = NULL, nik = NULL WHERE role IN ('admin', 'verifikator') AND nisn IN ('1234567890', '1234567891')");
            } catch (\Throwable $e) {
                // Ignore if already altered or not supported
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('data_pribadi')) {
            Schema::table('data_pribadi', function (Blueprint $table) {
                if (Schema::hasColumn('data_pribadi', 'jumlah_saudara')) {
                    $table->dropColumn('jumlah_saudara');
                }
                if (Schema::hasColumn('data_pribadi', 'anak_ke')) {
                    $table->dropColumn('anak_ke');
                }
            });
        }

        if (Schema::hasTable('organisasi')) {
            Schema::table('organisasi', function (Blueprint $table) {
                if (Schema::hasColumn('organisasi', 'periode_jabatan')) {
                    $table->dropColumn('periode_jabatan');
                }
            });
        }
    }
};
