<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verifikasi extends Model
{
    protected $table = 'verifikasi';
    
    protected $fillable = [
        'pendaftaran_id',
        'verifikator_id',
        'status', // 'pending', 'approved', 'needs_revision', 'rejected'
        'catatan_verifikasi', // Catatan internal verifikator
        'catatan_revisi', // Catatan yang dikirim ke pendaftar
        'data_pribadi_ok',
        'data_orang_tua_ok',
        'sekolah_asal_ok',
        'nilai_raport_ok',
        'prestasi_organisasi_ok',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'data_pribadi_ok' => 'boolean',
        'data_orang_tua_ok' => 'boolean',
        'sekolah_asal_ok' => 'boolean',
        'nilai_raport_ok' => 'boolean',
        'prestasi_organisasi_ok' => 'boolean',
    ];
    
    // Timestamps sudah ada di tabel (created_at, updated_at)
    public $timestamps = true;

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    // Helper Methods
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function needsRevision(): bool
    {
        return $this->status === 'needs_revision';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    // Check if all data is complete
    public function isAllDataComplete(): bool
    {
        return $this->data_pribadi_ok &&
               $this->data_orang_tua_ok &&
               $this->sekolah_asal_ok &&
               $this->nilai_raport_ok &&
               $this->prestasi_organisasi_ok;
    }
}