<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ListPendaftaran extends ListRecords
{
    protected static string $resource = PendaftaranResource::class;

    protected static ?string $title = 'Pendaftaran Saya';

    // ✅ Auto-redirect dihapus agar list table bisa muncul dengan tombol actions

    public function mount(): void
    {
        // Cek apakah pendaftaran sudah ditutup
        $closedDate = config('app.register_closed_date');

        if ($closedDate && Carbon::now()->greaterThanOrEqualTo(Carbon::parse($closedDate))) {
            Notification::make()
                ->title('Pendaftaran Telah Ditutup')
                ->body('Pendaftaran telah ditutup sejak ' . Carbon::parse($closedDate)->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i') . '. Anda hanya dapat mengakses dashboard.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(route('filament.pendaftar.pages.dashboard'), navigate: true);
            return;
        }

        parent::mount();
    }
}