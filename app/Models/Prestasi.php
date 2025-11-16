<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestasi extends Model
{
    protected $table = 'prestasi';
    
    protected $fillable = [
        'pendaftaran_id',
        'urutan',
        'nama_prestasi',
        'tingkat_prestasi',
        'file_bukti_prestasi',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}