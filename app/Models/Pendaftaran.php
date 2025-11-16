<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';
    
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

    public function organisasi(): HasOne
    {
        return $this->hasOne(Organisasi::class);
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
    $nilaiCount = $this->nilaiRaport()->count();
    
    // ✅ MINIMAL 4 SEMESTER (bukan 5)
    if ($nilaiCount < 4) {
        return false;
    }
    
    // Check if all required semesters have data
    for ($semester = 1; $semester <= 4; $semester++) {  // ✅ HANYA 1-4
        $nilai = $this->nilaiRaport()->where('semester', $semester)->first();
        
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
    }

    /**
     * Check if ALL registration data is complete
     */
    public function isAllDataComplete(): bool
    {
        return $this->isDataPribadiComplete() &&
               $this->isDataOrangTuaComplete() &&
               $this->isDataSekolahAsalComplete() &&
               $this->isNilaiRaportComplete() &&
               $this->isPrestasiOrganisasiComplete();
    }

    /**
     * Get completion status for each section
     */
    public function getCompletionStatus(): array
    {
        return [
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
    $totalSections = 5;
    $completedSections = 0;
    
    // 1. Data Pribadi
    if ($this->isDataPribadiComplete()) {
        $completedSections++;
    }
    
    // 2. Data Orang Tua
    if ($this->isDataOrangTuaComplete()) {
        $completedSections++;
    }
    
    // 3. Data Sekolah Asal
    if ($this->isDataSekolahAsalComplete()) {
        $completedSections++;
    }
    
    // 4. Nilai Raport
    if ($this->isNilaiRaportComplete()) {
        $completedSections++;
    }
    
    // 5. Prestasi & Organisasi
    if ($this->prestasi_organisasi_completed) {
        $completedSections++;
    }
    
    return (int) round(($completedSections / $totalSections) * 100);
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