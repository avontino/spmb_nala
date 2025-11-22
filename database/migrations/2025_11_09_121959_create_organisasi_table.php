<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            
            $table->string('nama_organisasi')->nullable();
            $table->enum('jabatan', [
                'Ketua',
                'Wakil Ketua',
                'Sekretaris',
                'Bendahara',
                'Koordinator Bidang'
            ])->nullable();
            
            // File Upload
            $table->string('file_bukti_organisasi')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisasi');
    }
};