<?php
namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\AdminStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [AdminStatsWidget::class];
    }
}