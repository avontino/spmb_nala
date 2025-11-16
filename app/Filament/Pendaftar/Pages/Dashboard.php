<?php

namespace App\Filament\Pendaftar\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Pendaftar\Widgets\RevisiNotificationWidget;
use App\Filament\Pendaftar\Widgets\StatusPendaftaranWidget;
use App\Filament\Pendaftar\Widgets\InformasiPentingWidget;


class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $title = 'Dashboard';
    
    public function getWidgets(): array
    {
        return [
            RevisiNotificationWidget::class,
            StatusPendaftaranWidget::class,
            InformasiPentingWidget::class,
            
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 2,
        ];
    }
}