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
        // Pastikan tabel verifikasi punya struktur yang benar
        if (!Schema::hasTable('verifikasi')) {
            Schema::create('verifikasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->onDelete('cascade');
                $table->foreignId('verifikator_id')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('status', ['pending', 'approved', 'needs_revision', 'rejected'])->default('pending');
                $table->text('catatan_verifikasi')->nullable(); // Catatan internal verifikator
                $table->text('catatan_revisi')->nullable(); // Catatan untuk pendaftar
                
                // ✅ Field boolean untuk verifikasi per bagian
                $table->boolean('data_pribadi_ok')->default(false);
                $table->boolean('data_orang_tua_ok')->default(false);
                $table->boolean('sekolah_asal_ok')->default(false);
                $table->boolean('nilai_raport_ok')->default(false);
                $table->boolean('prestasi_organisasi_ok')->default(false);
                
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        } else {
            // Update existing table jika belum ada kolom boolean
            Schema::table('verifikasi', function (Blueprint $table) {
                if (!Schema::hasColumn('verifikasi', 'data_pribadi_ok')) {
                    $table->boolean('data_pribadi_ok')->default(false)->after('catatan_revisi');
                }
                if (!Schema::hasColumn('verifikasi', 'data_orang_tua_ok')) {
                    $table->boolean('data_orang_tua_ok')->default(false)->after('data_pribadi_ok');
                }
                if (!Schema::hasColumn('verifikasi', 'sekolah_asal_ok')) {
                    $table->boolean('sekolah_asal_ok')->default(false)->after('data_orang_tua_ok');
                }
                if (!Schema::hasColumn('verifikasi', 'nilai_raport_ok')) {
                    $table->boolean('nilai_raport_ok')->default(false)->after('sekolah_asal_ok');
                }
                if (!Schema::hasColumn('verifikasi', 'prestasi_organisasi_ok')) {
                    $table->boolean('prestasi_organisasi_ok')->default(false)->after('nilai_raport_ok');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: remove columns if needed
        Schema::table('verifikasi', function (Blueprint $table) {
            $table->dropColumn([
                'data_pribadi_ok',
                'data_orang_tua_ok',
                'sekolah_asal_ok',
                'nilai_raport_ok',
                'prestasi_organisasi_ok',
            ]);
        });
    }
};
