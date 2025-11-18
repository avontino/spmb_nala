<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('verifikator_id')->constrained('users')->cascadeOnDelete()->nullable(true);
            
            $table->enum('status', ['approved', 'needs_revision', 'rejected']);
            $table->text('catatan')->nullable();
            $table->timestamp('verified_at');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi');
    }
};