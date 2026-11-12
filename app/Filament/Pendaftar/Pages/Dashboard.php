<?php

namespace App\Filament\Pendaftar\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Pendaftar\Widgets\RevisiNotificationWidget;
use App\Filament\Pendaftar\Widgets\StatusPendaftaranWidget;
use App\Filament\Pendaftar\Widgets\InformasiPentingWidget;
use App\Filament\Pendaftar\Widgets\PengumumanDashboardWidget;
use App\Filament\Pendaftar\Widgets\RegistrationClosedWidget;
use App\Models\Pendaftaran;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Session;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static string $view = 'filament.pendaftar.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            PengumumanDashboardWidget::class,
            RegistrationClosedWidget::class, // Widget penutupan pendaftaran
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