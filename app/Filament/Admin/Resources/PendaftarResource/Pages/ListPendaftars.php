<?php

namespace App\Filament\Admin\Resources\PendaftarResource\Pages;

use App\Filament\Admin\Resources\PendaftarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPendaftars extends ListRecords
{
    protected static string $resource = PendaftarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
