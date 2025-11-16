<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataSekolahAsal extends Model
{
    protected $table = 'data_sekolah_asal';
    
    protected $fillable = [
        'pendaftaran_id',
        'jenjang_sekolah',
        'nama_sekolah',
        'status_sekolah',
        'npsn',                       // ✅ BARU
        'alamat_sekolah',
        'kota_sekolah',               // ✅ BARU
        'provinsi_sekolah',           // ✅ BARU
        'file_transkrip_nilai',       // Akan dipindah ke EditNilaiRaport
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}