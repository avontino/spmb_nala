<?php

namespace App\Filament\Pendaftar\Widgets;

use Filament\Widgets\Widget;

class InformasiPentingWidget extends Widget
{
    protected static string $view = 'filament.pendaftar.widgets.informasi-penting-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 2;
    
}