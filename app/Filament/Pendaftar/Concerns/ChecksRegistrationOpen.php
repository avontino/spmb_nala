<?php

namespace App\Filament\Pendaftar\Concerns;

use App\Models\AppSetting;
use Filament\Notifications\Notification;

trait ChecksRegistrationOpen
{
    public function mount(...$parameters): void
    {
        if (AppSetting::isRegistrationClosed()) {
            Notification::make()
                ->title('Pendaftaran Telah Ditutup')
                ->danger()
                ->body(AppSetting::getClosedMessage())
                ->persistent()
                ->send();

            $this->redirect(route('filament.pendaftar.pages.dashboard'), navigate: true);
            return;
        }

        // Panggil parent mount jika pendaftaran masih dibuka
        parent::mount(...$parameters);
    }
}
