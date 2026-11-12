<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use App\Models\AppSetting;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListPendaftaran extends ListRecords
{
    protected static string $resource = PendaftaranResource::class;

    protected static ?string $title = 'Pendaftaran Saya';

    // ✅ Auto-redirect dihapus agar list table bisa muncul dengan tombol actions

    public function mount(): void
    {
        if (AppSetting::isRegistrationClosed()) {
            Notification::make()
                ->title('Pendaftaran Telah Ditutup')
                ->body(AppSetting::getClosedMessage())
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(route('filament.pendaftar.pages.dashboard'), navigate: true);
            return;
        }

        parent::mount();
    }
}