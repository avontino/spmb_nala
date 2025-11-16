<?php

namespace App\Filament\Verifikator\Widgets;

use App\Models\Pendaftaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VerifikasiStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('⏳ Menunggu Verifikasi', Pendaftaran::where('status', 'submitted')->count())
                ->description('Pendaftaran baru yang perlu diverifikasi')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
                
            Stat::make('✅ Disetujui', Pendaftaran::where('status', 'approved')->count())
                ->description('Total pendaftaran yang disetujui')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([1, 2, 3, 4, 5, 6, 7]),
                
            Stat::make('⚠️ Perlu Perbaikan', Pendaftaran::where('status', 'needs_revision')->count())
                ->description('Menunggu perbaikan dari pendaftar')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->chart([2, 1, 3, 2, 1, 2, 1]),
                
            Stat::make('❌ Ditolak', Pendaftaran::where('status', 'rejected')->count())
                ->description('Total pendaftaran yang ditolak')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('gray')
                ->chart([0, 1, 0, 1, 0, 0, 1]),
        ];
    }
}
