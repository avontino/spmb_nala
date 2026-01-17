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
        Schema::create('penerimaan_tahap2', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran');
            $table->foreignId('penerimaan_id')->constrained('penerimaan')->onDelete('cascade');
            $table->enum('status', ['lolos', 'tidak_lolos', 'cadangan'])->default('lolos');
            $table->string('nomor_va')->nullable();
            $table->string('sesi')->nullable();
            $table->foreignId('last_update_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_tahap2');
    }
};
