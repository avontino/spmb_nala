<?php

namespace App\Filament\Admin\Resources\PenerimaanTahap2Resource\Pages;

use App\Filament\Admin\Resources\PenerimaanTahap2Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenerimaanTahap2 extends EditRecord
{
    protected static string $resource = PenerimaanTahap2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
