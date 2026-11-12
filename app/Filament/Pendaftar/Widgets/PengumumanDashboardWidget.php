<?php

namespace App\Filament\Pendaftar\Widgets;

use App\Models\AppSetting;
use Filament\Widgets\Widget;

class PengumumanDashboardWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.pengumuman-dashboard-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        $content = AppSetting::getPendaftarDashboardContent();

        return ! empty($content['announcement_enabled']) && filled($content['announcement_body'] ?? null);
    }

    protected function getViewData(): array
    {
        return [
            'content' => AppSetting::getPendaftarDashboardContent(),
        ];
    }
}
