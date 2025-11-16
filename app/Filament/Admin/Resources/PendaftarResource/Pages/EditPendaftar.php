<?php

namespace App\Filament\Admin\Resources\PendaftarResource\Pages;

use App\Filament\Admin\Resources\PendaftarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPendaftar extends EditRecord
{
    protected static string $resource = PendaftarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
