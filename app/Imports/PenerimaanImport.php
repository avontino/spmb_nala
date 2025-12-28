<?php

namespace App\Imports;

use App\Models\Penerimaan;
use App\Models\Pendaftaran;
use App\Models\ImportLog;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;

class PenerimaanImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading, WithEvents, ShouldQueue
{
    public int $inserted = 0;
    public int $failed = 0;
    public string $importId;
    public int $userId;

    public function __construct(string $importId, int $userId)
    {
        $this->importId = $importId;
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        // Cari atau buat import log
        $importLog = ImportLog::firstOrCreate(
            ['import_id' => $this->importId],
            [
                'user_id' => $this->userId,
                'type' => 'penerimaan',
                'status' => 'processing',
            ]
        );

        foreach ($rows as $row) {
            $row['user_id'] = $this->userId;
            $result = $this->processRow($row);

            // Update progress di database
            $importLog->incrementProcessed();
            if ($result) {
                $importLog->incrementInserted();
                $this->inserted++;
            } else {
                $importLog->incrementFailed();
                $this->failed++;
            }
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    private function processRow($row)
    {
        $nomorPendaftaran = $row['nomor_pendaftaran'];

        $existingPenerimaan = Penerimaan::where('nomor_pendaftaran', $nomorPendaftaran)->first();
        if ($existingPenerimaan) {
            return false;
        }

        $pendaftaran = Pendaftaran::where('nomor_pendaftaran', $nomorPendaftaran)->first();

        if (!$pendaftaran) {
            return false;
        }

        $dataPribadi = $pendaftaran->dataPribadi;
        if ($dataPribadi) {
            if (isset($row['nama']) && strtolower(trim($row['nama'])) !== strtolower(trim($dataPribadi->nama_lengkap))) {
                return false;
            }
        }

        if ($pendaftaran->user->nisn && isset($row['nisn']) && trim($row['nisn']) !== trim($pendaftaran->user->nisn)) {
            return false;
        }

        Penerimaan::create([
            'nomor_pendaftaran' => $nomorPendaftaran,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => $row['status'] ?? 'lolos',
            'nomor_va' => $row['nomor_va'] ?? null,
            'sesi' => $row['sesi'] ?? null,
            'last_update_by' => $row['user_id'],
        ]);

        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_pendaftaran' => 'required',
            'nama' => 'required',
            'nisn' => 'required',
            'status' => 'required|in:lolos,tidak_lolos',
            'nomor_va' => 'nullable',
            'sesi' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nomor_pendaftaran.required' => 'Nomor pendaftaran wajib diisi',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus salah satu dari: lolos, tidak_lolos',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                // Import selesai, update status dan kirim notifikasi
                $importLog = ImportLog::where('import_id', $this->importId)->first();

                if ($importLog) {
                    $importLog->update(['status' => 'completed']);

                    // Kirim notifikasi ke user
                    $user = User::find($this->userId);
                    if ($user) {
                        Notification::make()
                            ->title('Import Excel Selesai')
                            ->body("Berhasil diinsert: {$importLog->inserted_rows}, Gagal: {$importLog->failed_rows}")
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
                }
            },

            ImportFailed::class => function (ImportFailed $event) {
                // Import gagal, update status dan kirim notifikasi error
                $importLog = ImportLog::where('import_id', $this->importId)->first();

                if ($importLog) {
                    $importLog->update([
                        'status' => 'failed',
                        'error_message' => $event->getException()->getMessage(),
                    ]);

                    // Kirim notifikasi error ke user
                    $user = User::find($this->userId);
                    if ($user) {
                        Notification::make()
                            ->title('Import Excel Gagal')
                            ->body($event->getException()->getMessage())
                            ->danger()
                            ->sendToDatabase($user);
                    }
                }
            },
        ];
    }
}
