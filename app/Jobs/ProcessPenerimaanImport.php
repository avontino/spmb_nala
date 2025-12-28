<?php

namespace App\Jobs;

use App\Imports\PenerimaanImport;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class ProcessPenerimaanImport implements ShouldQueue
{
    use Queueable;

    public $timeout = 600;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath,
        public string $importId,
        public int $userId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $importLog = null;

        try {
            // Cari atau buat import log
            $importLog = ImportLog::where('import_id', $this->importId)->first();

            if (!$importLog) {
                $importLog = ImportLog::create([
                    'import_id' => $this->importId,
                    'user_id' => $this->userId,
                    'type' => 'penerimaan',
                    'status' => 'processing',
                    'file_name' => basename($this->filePath),
                ]);
            } else {
                $importLog->update(['status' => 'processing']);
            }

            $import = new PenerimaanImport($this->importId, $this->userId);

            Excel::import($import, $this->filePath);

            // Hapus file temporary
            if (Storage::disk('local')->exists($this->filePath)) {
                Storage::disk('local')->delete($this->filePath);
            }

            // Update status
            $importLog->update(['status' => 'completed']);

            // Refresh untuk mendapatkan data terbaru dari database
            $importUpdated = ImportLog::where('import_id', $this->importId)->first();

            // Kirim notifikasi ke user
            $user = User::find($this->userId);
            if ($user) {
                Notification::make()
                    ->title('Import Excel Selesai')
                    ->body("Berhasil diinsert: {$importUpdated->inserted_rows}, Gagal: {$importUpdated->failed_rows}")
                    ->success()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->button()
                            ->label('Lihat Data Penerimaan')
                            ->url('/admin/penerimaans', shouldOpenInNewTab: false)
                            ->close()
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($user);
            }

        } catch (\Exception $e) {
            if ($importLog) {
                $importLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            // Hapus file jika ada error
            if (Storage::disk('local')->exists($this->filePath)) {
                Storage::disk('local')->delete($this->filePath);
            }

            // Kirim notifikasi error ke user
            $user = User::find($this->userId);
            if ($user) {
                Notification::make()
                    ->title('Import Excel Gagal')
                    ->body($e->getMessage())
                    ->danger()
                    ->sendToDatabase($user);
            }

            throw $e;
        }
    }
}
