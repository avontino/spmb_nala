<?php

namespace App\Filament\Pendaftar\Widgets;

use Filament\Widgets\Widget;

class StatusPendaftaranWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.status-pendaftaran-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 1;
    
    protected static ?string $pollingInterval = '30s';
}