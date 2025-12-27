<?php

namespace App\Imports;

use App\Models\Penerimaan;
use App\Models\Pendaftaran;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PenerimaanImport implements ToCollection, WithHeadingRow, WithValidation
{
    public int $inserted = 0;
    public int $failed = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $result = $this->processRow($row);
            if ($result) {
                $this->inserted++;
            } else {
                $this->failed++;
            }
        }
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
            'last_update_by' => Auth::id(),
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
