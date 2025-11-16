<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_raport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->integer('semester');
            
            // Nilai per mata pelajaran
            $table->decimal('nilai_agama', 5, 2)->nullable();
            $table->decimal('nilai_ppkn', 5, 2)->nullable();
            $table->decimal('nilai_bahasa_indonesia', 5, 2)->nullable();
            $table->decimal('nilai_matematika', 5, 2)->nullable();
            $table->decimal('nilai_ipa', 5, 2)->nullable();
            $table->decimal('nilai_ips', 5, 2)->nullable();
            $table->decimal('nilai_bahasa_inggris', 5, 2)->nullable();
            $table->decimal('nilai_seni_budaya', 5, 2)->nullable();
            $table->decimal('nilai_pjok', 5, 2)->nullable();
            $table->decimal('nilai_prakarya', 5, 2)->nullable();
            
            // Rata-rata otomatis
            $table->decimal('rata_rata', 5, 2)->nullable();
            
            $table->timestamps();
            
            $table->unique(['pendaftaran_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_raport');
    }
};