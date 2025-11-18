<?php

namespace App\Exports;

use App\Models\Pendaftaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PendaftaranExport implements FromCollection, withHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Pendaftaran::all()->map(function($pendaftaran) {
            return [
                $pendaftaran->nomor_pendaftaran,
                $pendaftaran->user->name,
                $pendaftaran->user->nik,
                $pendaftaran->user->nokk,
                $pendaftaran->user->nisn,
                $pendaftaran->user->email,
                $pendaftaran->submitted_at,
            ];
        });
    }

    public function headings() : array 
    {
        return [
            'No. Pendaftaran',
            'Nama',
            'NIK',
            'No. KK',
            'NISN',
            'Email',
            'Tanggal Submit',
        ];
    }

    // map each row to include only the desired fields
    public function map($pendaftaran): array
    {
        return [
            
        ];
    }



}
