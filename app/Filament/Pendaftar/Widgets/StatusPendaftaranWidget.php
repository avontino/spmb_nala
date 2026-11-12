<?php

namespace App\Filament\Pendaftar\Widgets;

use App\Models\AppSetting;
use Filament\Widgets\Widget;
use Carbon\Carbon;

class StatusPendaftaranWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.status-pendaftaran-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    /**
     * Cek apakah pendaftaran sudah ditutup
     */
    public function isRegistrationClosed(): bool
    {
        return AppSetting::isRegistrationClosed();
    }

    /**
     * Get tanggal penutupan formatted
     */
    public function getClosedDate(): string
    {
        $closedDate = AppSetting::getRegisterClosedDate();

        return $closedDate
            ? $closedDate->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i')
            : '-';
    }

    /**
     * Get jumlah hari sejak ditutup
     */
    public function getDaysSinceClosed(): int
    {
        $closedDate = AppSetting::getRegisterClosedDate();

        return $closedDate ? Carbon::now()->diffInDays($closedDate) : 0;
    }
}