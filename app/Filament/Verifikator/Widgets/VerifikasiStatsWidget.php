<?php

namespace App\Filament\Verifikator\Widgets;

use App\Models\Pendaftaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VerifikasiStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $counts = Pendaftaran::getStatusCounts();

        return [
            Stat::make('Menunggu Verifikasi', $counts['submitted'])
                ->description('Pendaftaran baru yang perlu diverifikasi')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->chartColor('warning'),

            Stat::make('Disetujui', $counts['approved'])
                ->description('Total pendaftaran yang disetujui')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->chartColor('success'),

            Stat::make('Perlu Perbaikan', $counts['needs_revision'])
                ->description('Menunggu perbaikan dari pendaftar')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->chart([2, 1, 3, 2, 1, 2, 1])
                ->chartColor('danger'),

            Stat::make('Ditolak', $counts['rejected'])
                ->description('Total pendaftaran yang ditolak')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('gray')
                ->chart([0, 1, 0, 1, 0, 0, 1])
                ->chartColor('gray'),

            Stat::make('Draft', $counts['draft'])
                ->description('Sedang melengkapi data')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info')
                ->chart([1, 3, 3, 4, 6, 4, 5])
                ->chartColor('info'),

            Stat::make('Total Pendaftar', $counts['total'])
                ->description('Seluruh data pendaftaran')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->chart([7, 3, 2, 4, 6, 3, 1])
                ->chartColor('primary'),
        ];
    }
}
