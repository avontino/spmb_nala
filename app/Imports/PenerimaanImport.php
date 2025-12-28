<?php

namespace App\Imports;

use App\Models\Penerimaan;
use App\Models\Pendaftaran;
use App\Models\ImportLog;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PenerimaanImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading, ShouldQueue
{
    public int $inserted = 0;
    public int $failed = 0;
    private string $importId;
    private int $userId;

    public function __construct(string $importId, int $userId)
    {
        $this->importId = $importId;
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        $importLog = ImportLog::where('import_id', $this->importId)->first();

        foreach ($rows as $row) {
            $result = $this->processRow($row);

            // Update progress di database
            if ($importLog) {
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
            'last_update_by' => $this->userId,
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
}
