<?php

namespace App\Filament\Admin\Resources\VerificatorResource\Pages;

use App\Filament\Admin\Resources\VerificatorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = VerificatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
