<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages\Concerns;

use Filament\Notifications\Notification;
use Carbon\Carbon;

trait PreventEditWhenSubmitted
{
    /**
     * Cek apakah pendaftaran sudah ditutup
     */
    protected function isRegistrationClosed(): bool
    {
        $closedDate = config('app.register_closed_date');

        if (!$closedDate) {
            return false;
        }

        return Carbon::now()->gte(Carbon::parse($closedDate));
    }
    /**
     * ⚠️ IMPORTANT: Property $verificationField HARUS didefinisikan di setiap page class
     * 
     * Contoh di EditDataPribadi:
     * protected string $verificationField = 'data_pribadi_ok';
     * 
     * JANGAN definisikan default value di trait ini untuk menghindari conflict!
     */

    /**
     * Check if form should be readonly
     * Form readonly jika:
     * 1. Status = submitted atau approved atau rejected
     * 2. Status = needs_revision TAPI section ini sudah OK (field_ok = 1)
     */
    public function isFormReadonly(): bool
    {
        // Jika status submitted atau approved atau rejected, semua readonly
        if (in_array($this->record->status, ['submitted', 'approved', 'rejected'])) {
            return true;
        }

        // Jika status needs_revision, cek per section
        if ($this->record->status === 'needs_revision' && isset($this->verificationField) && $this->verificationField) {
            $verifikasi = $this->record->latestVerifikasi;
            
            // Jika tidak ada data verifikasi, allow edit
            if (!$verifikasi) {
                return false;
            }

            // ✅ FIX: Section readonly HANYA jika sudah OK (true)
            // Jika false (perlu revisi), maka BISA EDIT
            return $verifikasi->{$this->verificationField} == true;
        }

        return false;
    }

    /**
     * Check if this section needs revision (field_ok = 0 atau false)
     */
    public function needsRevision(): bool
    {
        if ($this->record->status !== 'needs_revision') {
            return false;
        }

        if (!isset($this->verificationField) || !$this->verificationField) {
            return false;
        }

        $verifikasi = $this->record->latestVerifikasi;
        
        if (!$verifikasi) {
            return false;
        }

        // ✅ FIX: Return true jika field = false (0) = perlu diperbaiki
        // Ini berarti banner merah akan muncul dan form BISA DIEDIT
        return $verifikasi->{$this->verificationField} == false;
    }

    /**
     * Prevent save when status is submitted/approved
     * OR when section sudah OK (1)
     */
    protected function beforeSave(): void
    {
        // ✅ FIX: Hanya prevent jika readonly DAN bukan dalam mode revision
        if ($this->isFormReadonly() && !$this->needsRevision()) {
            Notification::make()
                ->title('⚠️ Form Tidak Bisa Diubah')
                ->warning()
                ->body('Bagian ini sudah diverifikasi atau sedang dalam proses verifikasi.')
                ->persistent()
                ->send();
                
            $this->halt();
        }
    }

    /**
     * Show notification when section needs revision
     */
    protected function showRevisionNotification(): void
    {
        if ($this->needsRevision()) {
            $verifikasi = $this->record->latestVerifikasi;
            
            Notification::make()
                ->title('🔴 Bagian Ini Perlu Diperbaiki')
                ->warning()
                ->body($verifikasi->catatan_revisi ?? 'Silakan perbaiki data pada bagian ini sesuai catatan verifikator.')
                ->persistent()
                ->send();
        }
    }

    /**
     * Show notification when page is readonly
     */
    protected function showReadonlyNotification(): void
    {
        if ($this->isFormReadonly() && !$this->needsRevision()) {
            Notification::make()
                ->title('📝 Mode Hanya Baca')
                ->info()
                ->body('Bagian ini sudah diverifikasi dan tidak dapat diubah.')
                ->persistent()
                ->send();
        }
    }

    /**
     * Disable form fields when readonly
     */
    public function mount(int | string $record): void
    {
        // ✅ PENTING: Panggil parent::mount dulu agar $this->record ter-set
        parent::mount($record);

        // Cek apakah pendaftaran sudah ditutup (setelah record ter-set)
        if ($this->isRegistrationClosed()) {
            $closedDate = Carbon::parse(config('app.register_closed_date'));

            Notification::make()
                ->title('Pendaftaran Telah Ditutup')
                ->body('Pendaftaran telah ditutup sejak ' . $closedDate->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i') . '. Anda hanya dapat mengakses dashboard.')
                ->danger()
                ->persistent()
                ->send();

            // Redirect ke dashboard
            $this->redirect(route('filament.pendaftar.pages.dashboard'), navigate: true);
            return;
        }

        // ✅ FIX: Hanya disable form jika readonly DAN bukan dalam mode revision
        if ($this->isFormReadonly() && !$this->needsRevision()) {
            // Make all form fields disabled
            $this->form->disabled();

            // Show appropriate notification
            $this->showReadonlyNotification();
        } else {
            // Show revision notification if needed
            $this->showRevisionNotification();
        }
    }

    /**
     * Get form actions - tambahkan tombol submit perbaikan
     */
    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();

        // Tambahkan tombol submit perbaikan jika needs_revision
        if ($this->needsRevision()) {
            $actions[] = \Filament\Actions\Action::make('submit_revision')
                ->label('✅ Kirim Perbaikan')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Kirim Perbaikan?')
                ->modalDescription('Pastikan data sudah benar sebelum mengirim perbaikan.')
                ->modalSubmitActionLabel('Ya, Kirim')
                ->action(function () {
                    $this->submitRevision();
                });
        }

        return $actions;
    }

    /**
     * Submit revision untuk section ini
     */
    protected function submitRevision(): void
    {
        $verifikasi = $this->record->latestVerifikasi;
        
        if (!$verifikasi) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Data verifikasi tidak ditemukan.')
                ->send();
            return;
        }

        // Update status field menjadi 1 (sudah diperbaiki, menunggu verifikasi ulang)
        $verifikasi->update([
            $this->verificationField => true,
        ]);

        // Cek apakah semua section sudah diperbaiki
        $allFixed = $verifikasi->data_pribadi_ok &&
                   $verifikasi->data_orang_tua_ok &&
                   $verifikasi->sekolah_asal_ok &&
                   $verifikasi->nilai_raport_ok &&
                   $verifikasi->prestasi_organisasi_ok;

        if ($allFixed) {
            // Jika semua sudah diperbaiki, ubah status pendaftaran ke submitted
            $this->record->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            Notification::make()
                ->title('✅ Semua Perbaikan Telah Dikirim!')
                ->success()
                ->body('Semua bagian sudah diperbaiki. Pendaftaran Anda akan diverifikasi ulang.')
                ->send();
        }

        Notification::make()
            ->title('✅ Perbaikan Berhasil Dikirim')
            ->success()
            ->body('Bagian ini telah diperbaiki. Lanjutkan memperbaiki bagian lainnya.')
            ->send();

        // Refresh form
        $this->form->disabled();
    }
}