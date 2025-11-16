<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_orang_tua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            
            // Data Ayah
            $table->string('nama_ayah');
            $table->enum('status_ayah', ['Masih Hidup', 'Sudah Meninggal']);
            $table->enum('pendidikan_ayah', [
                'Tidak Sekolah', 
                'SD/MI', 
                'SMP/MTs', 
                'SMA/SMK/MA', 
                'Diploma', 
                'S1', 
                'S2', 
                'S3'
            ])->nullable();
            $table->enum('pekerjaan_ayah', [
                'Buruh', 
                'Petani', 
                'Wiraswasta', 
                'PNS', 
                'Polri/TNI', 
                'Perangkat Desa', 
                'Nelayan',
                'Guru/Dosen',
                'Karyawan Swasta',
                'Dokter/Tenaga Medis', 
                'Lainnya'
            ])->nullable();
            $table->string('penghasilan_ayah')->nullable();
            
            // Data Ibu
            $table->string('nama_ibu');
            $table->enum('status_ibu', ['Masih Hidup', 'Sudah Meninggal']);
            $table->enum('pendidikan_ibu', [
                'Tidak Sekolah', 
                'SD/MI', 
                'SMP/MTs', 
                'SMA/SMK/MA', 
                'Diploma', 
                'S1', 
                'S2', 
                'S3'
            ])->nullable();
            $table->enum('pekerjaan_ibu', [
                'Ibu Rumah Tangga',
                'Buruh', 
                'Petani', 
                'Wiraswasta', 
                'PNS', 
                'Polri/TNI', 
                'Perangkat Desa', 
                'Nelayan', 
                'Guru/Dosen',
                'Karyawan Swasta',
                'Dokter/Tenaga Medis',
                'Lainnya'
            ])->nullable();
            $table->string('penghasilan_ibu')->nullable();
            
            // Data Kontak & Wali
            $table->string('no_hp_ortu', 20)->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('pendidikan_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->decimal('total_pengeluaran', 15, 2)->nullable();
            $table->decimal('total_pemasukan', 15, 2)->nullable();
            
            // File Upload
            $table->string('file_kk_ktp')->nullable();
            $table->string('file_ktp_wali')->nullable();
            $table->string('file_slip_gaji')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_orang_tua');
    }
};