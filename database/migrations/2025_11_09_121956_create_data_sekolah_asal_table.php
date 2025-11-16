<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_sekolah_asal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            
            $table->enum('jenjang_sekolah', ['SMP', 'MTs']);
            $table->string('nama_sekolah');
            $table->enum('status_sekolah', ['Negeri', 'Swasta']);
            $table->text('alamat_sekolah');
            
            // File Upload
            $table->string('file_transkrip_nilai')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_sekolah_asal');
    }
};