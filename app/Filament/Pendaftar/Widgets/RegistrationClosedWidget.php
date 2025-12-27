<?php

namespace App\Filament\Pendaftar\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;

class RegistrationClosedWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.registration-closed-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2; // Tampil setelah widget selamat datang

    /**
     * Cek apakah widget harus ditampilkan
     * ⚠️ DISABLED: Konten sudah dipindah ke InformasiPentingWidget
     */
    public static function canView(): bool
    {
        // Always hide karena sudah ada di InformasiPentingWidget
        return false;
    }

    /**
     * Get tanggal penutupan
     */
    public function getClosedDate(): string
    {
        $closedDate = config('app.register_closed_date');
        return Carbon::parse($closedDate)->format('d F Y');
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
