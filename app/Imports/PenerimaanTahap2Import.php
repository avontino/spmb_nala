<?php

namespace App\Imports;

use App\Models\Penerimaan;
use App\Models\PenerimaanTahap2;
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

class PenerimaanTahap2Import implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading, WithEvents, ShouldQueue
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
        $importLog = ImportLog::firstOrCreate(
            ['import_id' => $this->importId],
            [
                'user_id' => $this->userId,
                'type' => 'penerimaan_tahap2',
                'status' => 'processing',
            ]
        );

        foreach ($rows as $row) {
            $row['user_id'] = $this->userId;
            $result = $this->processRow($row);

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

        // Cek apakah nomor_pendaftaran sudah ada di tabel penerimaan (tahap 1)
        $penerimaan = Penerimaan::where('nomor_pendaftaran', $nomorPendaftaran)
            ->where('status', 'lolos')
            ->first();

        if (!$penerimaan) {
            return false;
        }

        // Cek apakah sudah ada di penerimaan tahap 2
        $existingTahap2 = PenerimaanTahap2::where('penerimaan_id', $penerimaan->id)->first();
        if ($existingTahap2) {
            return false;
        }

        // Validasi nama jika ada
        $dataPribadi = $penerimaan->pendaftaran?->dataPribadi;
        if ($dataPribadi) {
            if (isset($row['nama']) && strtolower(trim($row['nama'])) !== strtolower(trim($dataPribadi->nama_lengkap))) {
                return false;
            }
        }

        // Validasi NISN jika ada
        if ($penerimaan->pendaftaran?->user?->nisn && isset($row['nisn']) && trim($row['nisn']) !== trim($penerimaan->pendaftaran->user->nisn)) {
            return false;
        }

        PenerimaanTahap2::create([
            'nomor_pendaftaran' => $nomorPendaftaran,
            'penerimaan_id' => $penerimaan->id,
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
            'status' => 'required|in:lolos,tidak_lolos,cadangan',
            'nomor_va' => 'nullable',
            'sesi' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nomor_pendaftaran.required' => 'Nomor pendaftaran wajib diisi',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus salah satu dari: lolos, tidak_lolos, cadangan',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $importLog = ImportLog::where('import_id', $this->importId)->first();

                if ($importLog) {
                    $importLog->update(['status' => 'completed']);

                    $user = User::find($this->userId);
                    if ($user) {
                        Notification::make()
                            ->title('Import Excel Penerimaan Tahap 2 Selesai')
                            ->body("Berhasil diinsert: {$importLog->inserted_rows}, Gagal: {$importLog->failed_rows}")
                            ->success()
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('view')
                                    ->button()
                                    ->label('Lihat Data Penerimaan Tahap 2')
                                    ->url('/admin/penerimaan-tahap2s', shouldOpenInNewTab: false)
                                    ->close()
                                    ->markAsRead(),
                            ])
                            ->sendToDatabase($user);
                    }
                }
            },

            ImportFailed::class => function (ImportFailed $event) {
                $importLog = ImportLog::where('import_id', $this->importId)->first();

                if ($importLog) {
                    $importLog->update([
                        'status' => 'failed',
                        'error_message' => $event->getException()->getMessage(),
                    ]);

                    $user = User::find($this->userId);
                    if ($user) {
                        Notification::make()
                            ->title('Import Excel Penerimaan Tahap 2 Gagal')
                            ->body($event->getException()->getMessage())
                            ->danger()
                            ->sendToDatabase($user);
                    }
                }
            },
        ];
    }
}
