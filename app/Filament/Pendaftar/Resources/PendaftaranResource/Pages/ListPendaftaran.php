<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use Filament\Resources\Pages\ListRecords;

class ListPendaftaran extends ListRecords
{
    protected static string $resource = PendaftaranResource::class;

    protected static ?string $title = 'Pendaftaran Saya';
    
    // ✅ Auto-redirect dihapus agar list table bisa muncul dengan tombol actions
}