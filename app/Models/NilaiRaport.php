<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiRaport extends Model
{
    protected $table = 'nilai_raport';
    
    protected $fillable = [
        'pendaftaran_id',
        'semester',
        'nilai_agama',
        'nilai_ppkn',
        'nilai_bahasa_indonesia',
        'nilai_matematika',
        'nilai_ipa',
        'nilai_ips',
        'nilai_bahasa_inggris',
        'nilai_seni_budaya',
        'nilai_pjok',
        'nilai_prakarya',
        'rata_rata',
    ];

    protected $casts = [
        'nilai_agama' => 'decimal:2',
        'nilai_ppkn' => 'decimal:2',
        'nilai_bahasa_indonesia' => 'decimal:2',
        'nilai_matematika' => 'decimal:2',
        'nilai_ipa' => 'decimal:2',
        'nilai_ips' => 'decimal:2',
        'nilai_bahasa_inggris' => 'decimal:2',
        'nilai_seni_budaya' => 'decimal:2',
        'nilai_pjok' => 'decimal:2',
        'nilai_prakarya' => 'decimal:2',
        'rata_rata' => 'decimal:2',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    // Auto calculate rata-rata
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($nilai) {
            $subjects = [
                $nilai->nilai_agama,
                $nilai->nilai_ppkn,
                $nilai->nilai_bahasa_indonesia,
                $nilai->nilai_matematika,
                $nilai->nilai_ipa,
                $nilai->nilai_ips,
                $nilai->nilai_bahasa_inggris,
                $nilai->nilai_seni_budaya,
                $nilai->nilai_pjok,
                $nilai->nilai_prakarya,
            ];

            $valid = array_filter($subjects, fn($n) => $n !== null);
            $nilai->rata_rata = count($valid) > 0 ? array_sum($valid) / count($valid) : 0;
        });
    }
}