<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    protected ?array $cachedCompletionStatus = null;

    /**
     * Request-scoped cache for the current user's registration with dashboard relations.
     *
     * @var array<int, ?self>
     */
    protected static array $userRequestCache = [];

    protected static function booted(): void
    {
        $flush = function (self $model): void {
            if ($model->user_id) {
                unset(static::$userRequestCache[$model->user_id]);
            }
        };

        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * Get counts grouped by status in a single query for dashboard stats widgets.
     *
     * @return array<string, int>
     */
    public static function getStatusCounts(): array
    {
        $rows = static::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        $counts = [
            'submitted' => (int) ($rows['submitted'] ?? 0),
            'approved' => (int) ($rows['approved'] ?? 0),
            'needs_revision' => (int) ($rows['needs_revision'] ?? 0),
            'rejected' => (int) ($rows['rejected'] ?? 0),
            'draft' => (int) ($rows['draft'] ?? 0),
        ];

        $counts['total'] = array_sum(array_map('intval', $rows));

        return $counts;
    }

    /**
     * Fetch the authenticated (or given) user's Pendaftaran once per request with dashboard relations.
     */
    public static function forUser(?int $userId = null): ?self
    {
        $userId ??= auth()->id();

        if (! $userId) {
            return null;
        }

        if (array_key_exists($userId, static::$userRequestCache)) {
            return static::$userRequestCache[$userId];
        }

        return static::$userRequestCache[$userId] = static::with([
            'dataPribadi',
            'dataOrangTua',
            'dataSekolahAsal',
            'nilaiRaport',
            'latestVerifikasi.verifikator',
            'penerimaan',
            'penerimaanTahap2',
        ])->where('user_id', $userId)->first();
    }
    
    protected $fillable = [
        'user_id',
        'nomor_pendaftaran',
        'status',
        'submitted_at',
        'verified_at',
        'prestasi_organisasi_completed', // ✅ TAMBAHAN: Flag untuk tandai sudah simpan
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'prestasi_organisasi_completed' => 'boolean', // ✅ TAMBAHAN
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataPribadi(): HasOne
    {
        return $this->hasOne(DataPribadi::class);
    }

    public function dataOrangTua(): HasOne
    {
        return $this->hasOne(DataOrangTua::class);
    }

    public function dataSekolahAsal(): HasOne
    {
        return $this->hasOne(DataSekolahAsal::class);
    }

    public function nilaiRaport(): HasMany
    {
        return $this->hasMany(NilaiRaport::class)->orderBy('semester');
    }

    public function prestasi(): HasMany
    {
        return $this->hasMany(Prestasi::class)->orderBy('urutan');
    }

    public function organisasi(): HasMany
    {
        return $this->hasMany(Organisasi::class);
    }

    public function dokumenPendukung(): HasOne
    {
        return $this->hasOne(DokumenPendukung::class);
    }

    public function verifikasi(): HasMany
    {
        return $this->hasMany(Verifikasi::class);
    }

    public function latestVerifikasi(): HasOne
    {
        return $this->hasOne(Verifikasi::class)->latestOfMany();
    }

    public function penerimaan(): HasOne
    {
        return $this->hasOne(Penerimaan::class);
    }

    public function penerimaanTahap2(): HasOneThrough
    {
        return $this->hasOneThrough(
            PenerimaanTahap2::class,
            Penerimaan::class,
            'pendaftaran_id', // Foreign key on penerimaan table
            'penerimaan_id',  // Foreign key on penerimaan_tahap2 table
            'id',             // Local key on pendaftaran table
            'id'              // Local key on penerimaan table
        );
    }

    // Status Helper Methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function needsRevision(): bool
    {
        return $this->status === 'needs_revision';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // ✅ COMPLETENESS CHECK METHODS
    
    /**
     * Check if Data Pribadi is complete
     */
    public function isDataPribadiComplete(): bool
    {
        if (!$this->dataPribadi) {
            return false;
        }
        
        $required = [
            'nama_lengkap',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'alamat_lengkap',
            'no_hp',
            'pilihan_jurusan',
        ];
        
        foreach ($required as $field) {
            if (empty($this->dataPribadi->$field)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if Data Orang Tua is complete
     */
    public function isDataOrangTuaComplete(): bool
    {
        if (!$this->dataOrangTua) {
            return false;
        }
        
        $required = [
            'nama_ayah',
            'status_ayah',
            'pendidikan_ayah',
            'pekerjaan_ayah',
            'nama_ibu',
            'status_ibu',
            'pendidikan_ibu',
            'pekerjaan_ibu',
            'no_hp_ortu',
        ];
        
        foreach ($required as $field) {
            if (empty($this->dataOrangTua->$field)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if Data Sekolah Asal is complete
     */
public function isDataSekolahAsalComplete(): bool
{
    if (!$this->dataSekolahAsal) {
        return false;
    }
    
    // Cek field wajib termasuk field baru
    return !empty($this->dataSekolahAsal->jenjang_sekolah) &&
           !empty($this->dataSekolahAsal->nama_sekolah) &&
           !empty($this->dataSekolahAsal->status_sekolah) &&
           !empty($this->dataSekolahAsal->npsn) &&              // ✅ FIELD BARU
           !empty($this->dataSekolahAsal->alamat_sekolah) &&
           !empty($this->dataSekolahAsal->kota_sekolah) &&       // ✅ FIELD BARU
           !empty($this->dataSekolahAsal->provinsi_sekolah);     // ✅ FIELD BARU
}

    /**
     * Check if Nilai Raport is complete (at least semester 1-5)
     */
public function isNilaiRaportComplete(): bool
{
    $nilaiBySemester = $this->nilaiRaport->keyBy('semester');

    // ✅ MINIMAL 4 SEMESTER (bukan 5)
    if ($nilaiBySemester->count() < 4) {
        return false;
    }

    // Check if all required semesters have data
    for ($semester = 1; $semester <= 4; $semester++) {  // ✅ HANYA 1-4
        $nilai = $nilaiBySemester->get($semester);

        if (!$nilai) {
            return false;
        }

        // Check if essential fields are filled
        if (empty($nilai->nilai_matematika) ||
            empty($nilai->nilai_bahasa_indonesia) ||
            empty($nilai->nilai_bahasa_inggris)) {
            return false;
        }
    }

    return true;
}

    /**
     * ✅ UPDATED: Check if Prestasi & Organisasi page has been saved
     * Wajib klik simpan meskipun data kosong
     */
    public function isPrestasiOrganisasiComplete(): bool
    {
        // Cek apakah flag prestasi_organisasi_completed sudah true
        // Flag ini di-set true ketika user klik Simpan di halaman Prestasi & Organisasi
        return (bool) $this->prestasi_organisasi_completed;
    }

    /**
     * ✅ NEW: Mark Prestasi & Organisasi as completed
     */
    public function markPrestasiOrganisasiCompleted(): void
    {
        $this->update(['prestasi_organisasi_completed' => true]);
        $this->cachedCompletionStatus = null;
    }

    public function refresh()
    {
        $this->cachedCompletionStatus = null;

        return parent::refresh();
    }

    /**
     * Check if ALL registration data is complete
     */
    public function isAllDataComplete(): bool
    {
        return ! in_array(false, $this->getCompletionStatus(), true);
    }

    /**
     * Get completion status for each section (memoized per model instance).
     */
    public function getCompletionStatus(bool $fresh = false): array
    {
        if ($fresh) {
            $this->cachedCompletionStatus = null;
        }

        return $this->cachedCompletionStatus ??= [
            'data_pribadi' => $this->isDataPribadiComplete(),
            'data_orang_tua' => $this->isDataOrangTuaComplete(),
            'sekolah_asal' => $this->isDataSekolahAsalComplete(),
            'nilai_raport' => $this->isNilaiRaportComplete(),
            'prestasi_organisasi' => $this->isPrestasiOrganisasiComplete(),
        ];
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage(): int
    {
        $status = $this->getCompletionStatus();
        $completedSections = count(array_filter($status));

        return (int) round(($completedSections / count($status)) * 100);
    }

    /**
     * Check if can submit registration
     */
    public function canSubmit(): bool
    {
        return $this->status === 'draft' && $this->isAllDataComplete();
    }

    /**
     * Check if can print registration proof
     */
    public function canPrintProof(): bool
    {
        return in_array($this->status, ['submitted', 'approved', 'rejected']);
    }

    // Auto-generate nomor pendaftaran
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pendaftaran) {
            if (empty($pendaftaran->nomor_pendaftaran)) {
                $pendaftaran->nomor_pendaftaran = self::generateNomorPendaftaran();
            }
        });
    }

    private static function generateNomorPendaftaran(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastNumber = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return sprintf('SPMB-%s%s-%04d', $year, $month, $lastNumber);
    }
}