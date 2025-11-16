<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_pribadi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            
            // Data Identitas
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            
            // Data Fisik & Kesehatan
            $table->enum('agama', [
                'Islam', 
                'Kristen Katolik', 
                'Kristen Protestan', 
                'Hindu', 
                'Budha', 
                'Kong Hu Chu'
            ]);
            $table->enum('golongan_darah', ['A', 'B', 'O', 'AB', 'Belum Tahu'])->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->integer('berat_badan')->nullable();
            $table->enum('status_dalam_keluarga', [
                'Anak Kandung', 
                'Anak Tiri', 
                'Anak Angkat'
            ]);
            $table->text('riwayat_penyakit')->nullable();
            $table->string('hobi')->nullable();
            
            // Alamat
            $table->text('alamat_lengkap');
            $table->string('desa_kelurahan');
            $table->string('kecamatan');
            $table->string('kota_kabupaten');
            $table->string('provinsi');
            $table->string('no_hp', 20);
            
            // Pilihan
            $table->string('pilihan_jurusan')->nullable();
            
            // File Upload
            $table->string('pas_foto')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_pribadi');
    }
};