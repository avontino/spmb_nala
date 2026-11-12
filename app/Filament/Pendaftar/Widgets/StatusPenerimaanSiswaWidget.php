<?php

namespace App\Filament\Pendaftar\Widgets;

use App\Models\Pendaftaran;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class StatusPenerimaanSiswaWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.status-penerimaan-siswa-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 0; // Tampil paling atas

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    public ?Pendaftaran $pendaftaran = null;

    public function mount(): void
    {
        $this->pendaftaran = Pendaftaran::forUser(Auth::id());
    }

    /**
     * Cek apakah widget harus ditampilkan
     */
    public static function canView(): bool
    {
        $pendaftaran = Pendaftaran::forUser(Auth::id());

        // Hanya tampilkan jika status approved atau rejected
        return $pendaftaran && in_array($pendaftaran->status, ['approved', 'rejected']);
    }

    /**
     * Get status penerimaan (approved/rejected)
     */
    public function getStatusPenerimaan(): ?string
    {
        return $this->pendaftaran?->status;
    }

    /**
     * Get pesan untuk status approved
     */
    public function getPesanApproved(): string
    {
        return 'Selamat! Anda telah diterima sebagai siswa baru. Silakan mengikuti instruksi selanjutnya yang akan dikirimkan melalui email atau hubungi sekolah untuk informasi lebih lanjut.';
    }

    /**
     * Get pesan untuk status rejected
     */
    public function getPesanRejected(): string
    {
        $verifikasi = $this->pendaftaran?->latestVerifikasi;
        $catatan = $verifikasi?->catatan_revisi ?? 'Mohon maaf, pendaftaran Anda belum dapat kami terima pada periode ini.';

        return $catatan;
    }

    /**
     * Get nomor pendaftaran
     */
    public function getNomorPendaftaran(): ?string
    {
        return $this->pendaftaran?->nomor_pendaftaran;
    }

    /**
     * Get tanggal submit
     */
    public function getTanggalSubmit(): ?string
    {
        return $this->pendaftaran?->submitted_at?->format('d F Y, H:i');
    }
}
