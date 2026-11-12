<?php

namespace App\Filament\Pendaftar\Widgets;

use App\Models\AppSetting;
use Filament\Widgets\Widget;

class InformasiPentingWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.informasi-penting-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    /**
     * Tampilkan widget jika pendaftaran buka atau admin mengizinkan tampil saat tutup.
     */
    public static function canView(): bool
    {
        if (! AppSetting::isRegistrationClosed()) {
            return true;
        }

        $content = AppSetting::getPendaftarDashboardContent();

        return ! empty($content['show_info_when_closed']);
    }

    protected function getViewData(): array
    {
        return [
            'content' => AppSetting::getPendaftarDashboardContent(),
        ];
    }
}