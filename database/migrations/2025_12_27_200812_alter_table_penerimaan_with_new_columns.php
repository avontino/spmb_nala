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
        Schema::table('penerimaan', function (Blueprint $table) {
            $table->string('nomor_va')->nullable(true)->after('status');
            $table->string('sesi')->nullable(true)->after('nomor_va');
            //update status enum to 'lolos' dan 'tidak_lolos'
            $table->enum('status', ['lolos', 'tidak_lolos'])->default('tidak_lolos')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerimaan', function (Blueprint $table) {
            $table->dropColumn('nomor_va');
            $table->dropColumn('sesi');
            //revert status enum to previous values
            $table->enum('status', ['diterima', 'tidak_diterima'])->default('tidak_diterima')->change();
        });
    }
};
