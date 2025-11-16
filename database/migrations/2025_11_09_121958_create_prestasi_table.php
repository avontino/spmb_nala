<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            
            $table->integer('urutan');
            $table->string('nama_prestasi');
            $table->enum('tingkat_prestasi', [
                'Kota/Kabupaten',
                'Provinsi',
                'Nasional',
                'Internasional'
            ]);
            
            // File Upload
            $table->string('file_bukti_prestasi')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};