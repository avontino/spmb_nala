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
        // ✅ UPDATE TABEL DATA_ORANG_TUA
        Schema::table('data_orang_tua', function (Blueprint $table) {
            // NIK Ayah dan Ibu
            $table->string('nik_ayah', 16)->nullable()->after('nama_ayah');
            $table->string('nik_ibu', 16)->nullable()->after('nama_ibu');
            
            // Pekerjaan lainnya (jika pilih "Lainnya")
            $table->string('pekerjaan_ayah_lainnya')->nullable()->after('pekerjaan_ayah');
            $table->string('pekerjaan_ibu_lainnya')->nullable()->after('pekerjaan_ibu');
            
            // Status wali
            $table->string('status_wali')->nullable()->after('nama_wali');
        });

        // ✅ UPDATE TABEL DATA_PRIBADI
        Schema::table('data_pribadi', function (Blueprint $table) {
            // Alergi
            $table->string('status_alergi')->nullable()->after('riwayat_penyakit');
            $table->text('keterangan_alergi')->nullable()->after('status_alergi');
            
            // Bahasa asing
            $table->string('bahasa_asing_dikuasai')->nullable()->after('keterangan_alergi');
            
            // Rename pilihan_jurusan ke studi_lanjut (optional - bisa juga di model accessor)
            // $table->renameColumn('pilihan_jurusan', 'studi_lanjut');
        });

        // ✅ UPDATE TABEL DATA_SEKOLAH_ASAL
        Schema::table('data_sekolah_asal', function (Blueprint $table) {
            // NPSN
            $table->string('npsn', 8)->nullable()->after('status_sekolah');
            
            // Kota dan Provinsi terpisah
            $table->string('kota_sekolah')->nullable()->after('alamat_sekolah');
            $table->string('provinsi_sekolah')->nullable()->after('kota_sekolah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_orang_tua', function (Blueprint $table) {
            $table->dropColumn([
                'nik_ayah',
                'nik_ibu',
                'pekerjaan_ayah_lainnya',
                'pekerjaan_ibu_lainnya',
                'status_wali',
            ]);
        });

        Schema::table('data_pribadi', function (Blueprint $table) {
            $table->dropColumn([
                'status_alergi',
                'keterangan_alergi',
                'bahasa_asing_dikuasai',
            ]);
        });

        Schema::table('data_sekolah_asal', function (Blueprint $table) {
            $table->dropColumn([
                'npsn',
                'kota_sekolah',
                'provinsi_sekolah',
            ]);
        });
    }
};