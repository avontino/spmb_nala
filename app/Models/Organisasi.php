<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organisasi extends Model
{
    protected $table = 'organisasi';
    
    protected $fillable = [
        'pendaftaran_id',
        'nama_organisasi',
        'jabatan',
        'periode_jabatan',
        'file_bukti_organisasi',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}