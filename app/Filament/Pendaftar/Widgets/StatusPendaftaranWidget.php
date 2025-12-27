<?php

namespace App\Filament\Pendaftar\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;

class StatusPendaftaranWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.status-pendaftaran-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    /**
     * Cek apakah pendaftaran sudah ditutup
     */
    public function isRegistrationClosed(): bool
    {
        $closedDate = config('app.register_closed_date');

        if (!$closedDate) {
            return false;
        }

        return Carbon::now()->gte(Carbon::parse($closedDate));
    }

    /**
     * Get tanggal penutupan formatted
     */
    public function getClosedDate(): string
    {
        $closedDate = config('app.register_closed_date');
        return Carbon::parse($closedDate)->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i');
    }

    /**
     * Get jumlah hari sejak ditutup
     */
    public function getDaysSinceClosed(): int
    {
        $closedDate = Carbon::parse(config('app.register_closed_date'));
        return Carbon::now()->diffInDays($closedDate);
    }
}