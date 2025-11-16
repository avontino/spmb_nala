<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataOrangTua extends Model
{
    protected $table = 'data_orang_tua';
    
    protected $fillable = [
        'pendaftaran_id',
        // Data Ayah
        'nama_ayah',
        'nik_ayah',                    // ✅ BARU
        'status_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'pekerjaan_ayah_lainnya',      // ✅ BARU
        'penghasilan_ayah',
        // Data Ibu
        'nama_ibu',
        'nik_ibu',                     // ✅ BARU
        'status_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'pekerjaan_ibu_lainnya',       // ✅ BARU
        'penghasilan_ibu',
        // Kontak
        'no_hp_ortu',
        // Data Wali
        'nama_wali',
        'status_wali',                 // ✅ BARU
        'pendidikan_wali',
        'pekerjaan_wali',
        // Keuangan
        'total_pengeluaran',
        'total_pemasukan',
        // Dokumen
        'file_kk_ktp',
        'file_ktp_wali',
        'file_slip_gaji',
    ];

    protected $casts = [
        'total_pengeluaran' => 'decimal:2',
        'total_pemasukan' => 'decimal:2',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}