<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DataPribadi extends Model
{
    protected $table = 'data_pribadi';
    
    protected $fillable = [
        'pendaftaran_id',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'golongan_darah',
        'tinggi_badan',
        'berat_badan',
        'status_dalam_keluarga',
        'anak_ke',
        'jumlah_saudara',
        'riwayat_penyakit',
        'status_alergi',              // ✅ BARU
        'keterangan_alergi',          // ✅ BARU
        'bahasa_asing_dikuasai',      // ✅ BARU
        'hobi',
        'alamat_lengkap',
        'desa_kelurahan',
        'kecamatan',
        'kota_kabupaten',
        'provinsi',
        'no_hp',
        'pilihan_jurusan',            // Tetap pakai nama lama di database, tapi label berubah di form
        'pas_foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function getPasFotoUrlAttribute(): ?string
    {
        if (!$this->pas_foto) {
            return null;
        }
        
        return asset('storage/' . $this->pas_foto);
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    protected static function booted(): void
    {
        static::saved(function (DataPribadi $dataPribadi) {
            if (!empty($dataPribadi->nama_lengkap)) {
                $user = $dataPribadi->pendaftaran?->user;
                if ($user && $user->name !== $dataPribadi->nama_lengkap) {
                    $user->update(['name' => $dataPribadi->nama_lengkap]);
                }
            }
        });
    }
}