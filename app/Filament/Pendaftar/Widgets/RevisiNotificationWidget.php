<?php

namespace App\Filament\Pendaftar\Widgets;

use App\Models\Pendaftaran;
use Filament\Widgets\Widget;

class RevisiNotificationWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.revisi-notification-widget';

    protected static ?int $sort = 0; // Tampil paling atas

    protected int | string | array $columnSpan = 'full';

    public function getPendaftaran()
    {
        return Pendaftaran::with('latestVerifikasi.verifikator')
            ->where('user_id', auth()->id())
            ->first();
    }

    public function isVisible(): bool
    {
        $pendaftaran = $this->getPendaftaran();
        
        return $pendaftaran && $pendaftaran->status === 'needs_revision';
    }

    /**
     * Get sections yang perlu diperbaiki
     */
    public function getSectionsNeedingRevision(): array
    {
        $pendaftaran = $this->getPendaftaran();
        $verifikasi = $pendaftaran?->latestVerifikasi;
        
        if (!$verifikasi) {
            return [];
        }

        $sections = [];

        if (!$verifikasi->data_pribadi_ok) {
            $sections[] = [
                'name' => 'Data Pribadi',
                'icon' => 'heroicon-o-user',
                'route' => 'edit-data-pribadi',
            ];
        }

        if (!$verifikasi->data_orang_tua_ok) {
            $sections[] = [
                'name' => 'Data Orang Tua',
                'icon' => 'heroicon-o-users',
                'route' => 'edit-data-orang-tua',
            ];
        }

        if (!$verifikasi->sekolah_asal_ok) {
            $sections[] = [
                'name' => 'Sekolah Asal',
                'icon' => 'heroicon-o-academic-cap',
                'route' => 'edit-sekolah-asal',
            ];
        }

        if (!$verifikasi->nilai_raport_ok) {
            $sections[] = [
                'name' => 'Nilai Raport',
                'icon' => 'heroicon-o-chart-bar',
                'route' => 'edit-nilai-raport',
            ];
        }

        if (!$verifikasi->prestasi_organisasi_ok) {
            $sections[] = [
                'name' => 'Prestasi & Organisasi',
                'icon' => 'heroicon-o-trophy',
                'route' => 'edit-prestasi',
            ];
        }

        return $sections;
    }
}