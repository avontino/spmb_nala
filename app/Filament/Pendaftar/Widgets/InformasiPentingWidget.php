<?php

namespace App\Filament\Pendaftar\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;

class InformasiPentingWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.informasi-penting-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    /**
     * Hide widget jika pendaftaran sudah ditutup
     */
    public static function canView(): bool
    {
        $closedDate = config('app.register_closed_date');

        if (!$closedDate) {
            return true; // Tampilkan jika tidak ada tanggal penutupan
        }

        // Hide jika sudah ditutup
        return Carbon::now()->lt(Carbon::parse($closedDate));
    }
}