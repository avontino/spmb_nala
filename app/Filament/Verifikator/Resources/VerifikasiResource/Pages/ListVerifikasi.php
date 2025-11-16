<?php

namespace App\Filament\Verifikator\Resources\VerifikasiResource\Pages;

use App\Filament\Verifikator\Resources\VerifikasiResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListVerifikasi extends ListRecords
{
    protected static string $resource = VerifikasiResource::class;

    protected static ?string $title = 'Verifikasi Pendaftaran';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->redirect($this->getResource()::getUrl('index'))),
        ];
    }
}
