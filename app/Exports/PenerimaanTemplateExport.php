<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class PenerimaanTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return new Collection([
            [
                'SPMB-202501-0001',
                'John Doe',
                '1234567890',
                'diterima',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'nomor_pendaftaran',
            'nama',
            'nisn',
            'status',
        ];
    }
}
