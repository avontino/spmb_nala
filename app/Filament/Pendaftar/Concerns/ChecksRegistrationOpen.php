<?php

namespace App\Filament\Pendaftar\Concerns;

use Carbon\Carbon;
use Filament\Notifications\Notification;

trait ChecksRegistrationOpen
{
    public function mount(...$parameters): void
    {
        // Cek apakah pendaftaran sudah ditutup
        $closedDate = config('app.register_closed_date');

        if ($closedDate) {
            $now = Carbon::now();
            $registerClosedDate = Carbon::parse($closedDate);

            if ($now->greaterThanOrEqualTo($registerClosedDate)) {
                // Tampilkan notifikasi
                Notification::make()
                    ->title('Pendaftaran Telah Ditutup')
                    ->danger()
                    ->body('Pendaftaran telah ditutup sejak ' . $registerClosedDate->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i') . '. Anda hanya dapat mengakses dashboard.')
                    ->persistent()
                    ->send();

                // Redirect ke dashboard
                $this->redirect(route('filament.pendaftar.pages.dashboard'), navigate: true);
                return;
            }
        }

        // Panggil parent mount jika pendaftaran masih dibuka
        parent::mount(...$parameters);
    }
}
