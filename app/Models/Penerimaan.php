<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penerimaan extends Model
{
    protected $table = 'penerimaan';

    protected $fillable = [
        'nomor_pendaftaran',
        'pendaftaran_id',
        'status',
        'last_update_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function lastUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_update_by');
    }
}
