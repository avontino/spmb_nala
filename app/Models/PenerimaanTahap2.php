<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenerimaanTahap2 extends Model
{
    protected $table = 'penerimaan_tahap2';

    protected $fillable = [
        'nomor_pendaftaran',
        'penerimaan_id',
        'status',
        'nomor_va',
        'sesi',
        'last_update_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->last_update_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->last_update_by = auth()->id();
        });
    }

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(Penerimaan::class);
    }

    public function lastUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_update_by');
    }
}
