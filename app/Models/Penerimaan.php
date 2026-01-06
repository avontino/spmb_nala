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
        'nomor_va',
        'sesi',
        'last_update_by',
        'sesi_interview',
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

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function lastUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_update_by');
    }
}
