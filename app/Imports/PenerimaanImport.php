<?php

namespace App\Imports;

use App\Models\Penerimaan;
use App\Models\Pendaftaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class PenerimaanImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $nomorPendaftaran = $row['nomor_pendaftaran'];

        $existingPenerimaan = Penerimaan::where('nomor_pendaftaran', $nomorPendaftaran)->first();
        if ($existingPenerimaan) {
            return null;
        }

        $pendaftaran = Pendaftaran::where('nomor_pendaftaran', $nomorPendaftaran)->first();

        if (!$pendaftaran) {
            return null;
        }

        $dataPribadi = $pendaftaran->dataPribadi;
        if ($dataPribadi) {
            if (isset($row['nama']) && strtolower(trim($row['nama'])) !== strtolower(trim($dataPribadi->nama_lengkap))) {
                return null;
            }
        }

        if ($pendaftaran->user->nisn && isset($row['nisn']) && trim($row['nisn']) !== trim($pendaftaran->user->nisn)) {
            return null;
        }

        return new Penerimaan([
            'nomor_pendaftaran' => $nomorPendaftaran,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => $row['status'] ?? 'diterima',
            'last_update_by' => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'nomor_pendaftaran' => 'required|string',
            'nama' => 'nullable|string',
            'nisn' => 'nullable|string',
            'status' => 'required|in:diterima,tidak_diterima,cadangan',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nomor_pendaftaran.required' => 'Nomor pendaftaran wajib diisi',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus salah satu dari: diterima, tidak_diterima, cadangan',
        ];
    }
}
