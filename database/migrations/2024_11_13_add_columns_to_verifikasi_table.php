<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration ini menambahkan kolom-kolom baru ke tabel verifikasi yang sudah ada
     */
    public function up(): void
    {
        Schema::table('verifikasi', function (Blueprint $table) {
            // Tambah kolom checklist verifikasi per bagian (setelah kolom status)
            $table->boolean('data_pribadi_ok')->default(false)->after('status');
            $table->boolean('data_orang_tua_ok')->default(false)->after('data_pribadi_ok');
            $table->boolean('sekolah_asal_ok')->default(false)->after('data_orang_tua_ok');
            $table->boolean('nilai_raport_ok')->default(false)->after('sekolah_asal_ok');
            $table->boolean('prestasi_organisasi_ok')->default(false)->after('nilai_raport_ok');
            
            // Tambah kolom catatan revisi (setelah catatan)
            // Rename kolom 'catatan' menjadi 'catatan_verifikasi' untuk lebih jelas
            $table->renameColumn('catatan', 'catatan_verifikasi');
            
            // Tambah catatan revisi baru
            $table->text('catatan_revisi')->nullable()->after('catatan_verifikasi')
                ->comment('Catatan yang dikirim ke pendaftar untuk revisi');
            
            // Update status enum untuk menambahkan 'pending'
            // Note: Di MySQL, kita perlu drop dan recreate enum
            // Atau bisa gunakan raw query
            DB::statement("ALTER TABLE verifikasi MODIFY COLUMN status ENUM('pending', 'approved', 'needs_revision', 'rejected') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifikasi', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropColumn([
                'data_pribadi_ok',
                'data_orang_tua_ok',
                'sekolah_asal_ok',
                'nilai_raport_ok',
                'prestasi_organisasi_ok',
                'catatan_revisi'
            ]);
            
            // Rename kembali
            $table->renameColumn('catatan_verifikasi', 'catatan');
            
            // Kembalikan status enum ke semula
            DB::statement("ALTER TABLE verifikasi MODIFY COLUMN status ENUM('approved', 'needs_revision', 'rejected') NOT NULL");
        });
    }
};
