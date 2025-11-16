<?php
namespace App\Filament\Verifikator\Pages;

use App\Filament\Verifikator\Widgets\VerifikasiStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [VerifikasiStatsWidget::class];
    }
}