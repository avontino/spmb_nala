<?php

namespace App\Exports;

use App\Models\Pendaftaran;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PendaftaranExport extends StringValueBinder implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, ShouldQueue, WithCustomValueBinder, WithChunkReading
{
    /**
     * Number of times the job may be attempted.
     * Reduced to 1 for production to prevent multiple failed exports with large data
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1800;

    /**
     * The number of seconds to wait before retrying a job that encountered an exception.
     *
     * @var int
     */
    public $backoff = 60;
    
    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return Pendaftaran::query()
            ->with([
                'user',
                'latestVerifikasi',
                'dataPribadi',
                'dataOrangTua',
                'dataSekolahAsal',
                'nilaiRaport',
                'prestasi',
                'organisasi',
                'dokumenPendukung',
            ]);
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
            'Status Pendaftaran',
            'Status Verifikasi',
            // data pribadi
            'Nama Lengkap',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Agama',
            'Golongan Darah',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'Status Dalam Keluarga',
            'Riwayat Penyakit',
            'Status Alergi',
            'Keterangan Alergi',
            'Bahasa Asing',
            'Hobi',
            'Alamat',
            'No. HP',
            'Desa/Kelurahan',
            'Kecamatan',
            'Kabupaten/Kota',
            'Provinsi',
            'Pas Fota (ada/tidak)',
            // data orang tua
            'Nama Ayah',
            'Pekerjaan Ayah',
            'NIK Ayah',
            'Status Ayah',
            'Pendidikan Ayah',
            'Pekerjaan Ayah',
            'Pekerjaan Ayah Lainnya',
            'Penghasilan Ayah',
            'Nama Ibu',
            'Pekerjaan Ibu',
            'NIK Ibu',
            'Status Ibu',
            'Pendidikan Ibu',
            'Pekerjaan Ibu',
            'Pekerjaan Ibu Lainnya',
            'Penghasilan Ibu',
            'No HP Orang Tua',
            // data wali
            'Nama Wali',
            'Status Wali',
            'Pendidikan Wali',
            'Pekerjaan Wali',
            'Total Pengeluaran',
            'Total Pemasukan',
            'File KK KTP (ada/tidak)',
            'File KTP Wali (ada/tidak)',
            'File Slip Gaji (ada/tidak)',
            // data sekolah asal
            'Jenjang Sekolah Asal',
            'Nama Sekolah Asal',
            'Status Sekolah Asal',
            'NPSN',
            'Alamat Sekolah Asal',
            'Kota/Kabupaten Sekolah Asal',
            'Provinsi Sekolah Asal',
            'File Transkrip Nilai (ada/tidak)',
            // data rapor
            'Nilai rata-rata semester 1',
            'Nilai rata-rata semester 2',
            'Nilai rata-rata semester 3',
            'Nilai rata-rata semester 4',
            // data prestasi
            'Nama Prestasi 1',
            'Tingkat Prestasi 1',
            'File Bukti Prestasi 1 (ada/tidak)',
            'Nama Prestasi 2',
            'Tingkat Prestasi 2',
            'File Bukti Prestasi 2 (ada/tidak)',
            'Nama Prestasi 3',
            'Tingkat Prestasi 3',
            'File Bukti Prestasi 3 (ada/tidak)',
            // data organisasi
            'Nama Organisasi',
            'Jabatan Organisasi',
            'File Bukti Organisasi (ada/tidak)',
            'File Transkrip Prestasi (ada/tidak)',
        ];
    }

    /**
    * Map each row to include only the desired fields
    */
    public function map($pendaftaran): array
    {
        return [
            $pendaftaran->nomor_pendaftaran,
            $pendaftaran->user->name,
            (string) $pendaftaran->user->nik,
            (string) $pendaftaran->user->nokk,
            (string) $pendaftaran->user->nisn,
            $pendaftaran->user->email,
            $pendaftaran->submitted_at ?? '-',
            ucwords(str_replace('_', ' ', $pendaftaran->status)),
            $pendaftaran->latestVerifikasi ? ucwords(str_replace('_', ' ', $pendaftaran->latestVerifikasi->status)) : 'Belum Diverifikasi',
            // data pribadi
            $pendaftaran->dataPribadi->nama_lengkap ?? '-',
            $pendaftaran->dataPribadi->tempat_lahir ?? '-',
            $pendaftaran->dataPribadi->tanggal_lahir ?? '-',
            $pendaftaran->dataPribadi->jenis_kelamin ?? '-',
            $pendaftaran->dataPribadi->agama ?? '-',
            $pendaftaran->dataPribadi->golongan_darah ?? '-',
            $pendaftaran->dataPribadi->tinggi_badan ?? '-',
            $pendaftaran->dataPribadi->berat_badan ?? '-',
            $pendaftaran->dataPribadi->status_dalam_keluarga ?? '-',
            $pendaftaran->dataPribadi->riwayat_penyakit ?? '-',
            $pendaftaran->dataPribadi->status_alergi ?? '-',
            $pendaftaran->dataPribadi->keterangan_alergi ?? '-',
            $pendaftaran->dataPribadi->bahasa_asing_dikuasai ?? '-',
            $pendaftaran->dataPribadi->hobi ?? '-',
            $pendaftaran->dataPribadi->alamat_lengkap ?? '-',
            $pendaftaran->dataPribadi->no_hp ?? '-',
            $pendaftaran->dataPribadi->desa_kelurahan ?? '-',
            $pendaftaran->dataPribadi->kecamatan ?? '-',
            $pendaftaran->dataPribadi->kabupaten_kota ?? '-',
            $pendaftaran->dataPribadi->provinsi ?? '-',
            $pendaftaran->dataPribadi ? ($pendaftaran->dataPribadi->pas_foto ? 'ada' : 'tidak') : null,
            // data orang tua
            $pendaftaran->dataOrangTua->nama_ayah ?? '-',
            $pendaftaran->dataOrangTua->pekerjaan_ayah ?? '-',
            (string) ($pendaftaran->dataOrangTua->nik_ayah ?? '-'),
            $pendaftaran->dataOrangTua->status_ayah ?? '-',
            $pendaftaran->dataOrangTua->pendidikan_ayah ?? '-',
            $pendaftaran->dataOrangTua->pekerjaan_ayah ?? '-',
            $pendaftaran->dataOrangTua->pekerjaan_ayah_lainnya ?? '-',
            $pendaftaran->dataOrangTua->penghasilan_ayah ?? '-',
            $pendaftaran->dataOrangTua->nama_ibu ?? '-',
            $pendaftaran->dataOrangTua->pekerjaan_ibu ?? '-',
            (string) ($pendaftaran->dataOrangTua->nik_ibu ?? '-'),
            $pendaftaran->dataOrangTua->status_ibu ?? '-',
            $pendaftaran->dataOrangTua->pendidikan_ibu ?? '-',
            $pendaftaran->dataOrangTua->pekerjaan_ibu ?? '-',
            $pendaftaran->dataOrangTua->pekerjaan_ibu_lainnya ?? '-',
            $pendaftaran->dataOrangTua->penghasilan_ibu ?? '-',
            $pendaftaran->dataOrangTua->no_hp_ortu ?? '-',
            // data wali
            $pendaftaran->dataOrangTua->nama_wali ?? '-',
            $pendaftaran->dataOrangTua->status_wali ?? '-',
            $pendaftaran->dataOrangTua->pendidikan_wali ?? '-',
            $pendaftaran->dataOrangTua->pekerjaan_wali ?? '-',
            $pendaftaran->dataOrangTua->total_pengeluaran ?? '-',
            $pendaftaran->dataOrangTua->total_pemasukan ?? '-',
            $pendaftaran->dataOrangTua ? ($pendaftaran->dataOrangTua->file_kk_ktp ? 'ada' : 'tidak') : null,
            $pendaftaran->dataOrangTua ? ($pendaftaran->dataOrangTua->file_ktp_wali  ? 'ada' : 'tidak') : null,
            $pendaftaran->dataOrangTua ? ($pendaftaran->dataOrangTua->file_slip_gaji  ? 'ada' : 'tidak') : null,
            // data sekolah asal
            $pendaftaran->dataSekolahAsal->jenjang_sekolah ?? '-',
            $pendaftaran->dataSekolahAsal->nama_sekolah ?? '-',
            $pendaftaran->dataSekolahAsal->status_sekolah ?? '-',
            (string) ($pendaftaran->dataSekolahAsal->npsn ?? '-'),
            $pendaftaran->dataSekolahAsal->alamat_sekolah ?? '-',
            $pendaftaran->dataSekolahAsal->kota_sekolah ?? '-',
            $pendaftaran->dataSekolahAsal->provinsi_sekolah ?? '-',
            $pendaftaran->dataSekolahAsal ? ($pendaftaran->dataSekolahAsal->file_transkrip_nilai ? 'ada' : 'tidak') : null,
            // data rapor
            $pendaftaran->nilaiRaport->where('semester', 1)->first()->rata_rata ?? null,
            $pendaftaran->nilaiRaport->where('semester', 2)->first()->rata_rata ?? null,
            $pendaftaran->nilaiRaport->where('semester', 3)->first()->rata_rata ?? null,
            $pendaftaran->nilaiRaport->where('semester', 4)->first()->rata_rata ?? null,
            // data prestasi
            $pendaftaran->prestasi->count() >= 1 ? $pendaftaran->prestasi->get(0)->nama_prestasi : null,
            $pendaftaran->prestasi->count() >= 1 ? $pendaftaran->prestasi->get(0)->tingkat_prestasi : null,
            $pendaftaran->prestasi->count() >= 1 ? ($pendaftaran->prestasi->get(0)->file_bukti_prestasi ? 'ada' : 'tidak') : null,
            $pendaftaran->prestasi->count() >= 2 ? $pendaftaran->prestasi->get(1)->nama_prestasi : null,
            $pendaftaran->prestasi->count() >= 2 ? $pendaftaran->prestasi->get(1)->tingkat_prestasi : null,
            $pendaftaran->prestasi->count() >= 2 ? ($pendaftaran->prestasi->get(1)->file_bukti_prestasi ? 'ada' : 'tidak') : null,
            $pendaftaran->prestasi->count() >= 3 ? $pendaftaran->prestasi->get(2)->nama_prestasi : null,
            $pendaftaran->prestasi->count() >= 3 ? $pendaftaran->prestasi->get(2)->tingkat_prestasi : null,
            $pendaftaran->prestasi->count() >= 3 ? ($pendaftaran->prestasi->get(2)->file_bukti_prestasi ? 'ada' : 'tidak') : null,
            // data organisasi
            ($pendaftaran->organisasi instanceof \Illuminate\Support\Collection ? $pendaftaran->organisasi->first() : $pendaftaran->organisasi)?->nama_organisasi,
            ($pendaftaran->organisasi instanceof \Illuminate\Support\Collection ? $pendaftaran->organisasi->first() : $pendaftaran->organisasi)?->jabatan,
            (($pendaftaran->organisasi instanceof \Illuminate\Support\Collection ? $pendaftaran->organisasi->first() : $pendaftaran->organisasi)?->file_bukti_organisasi ? 'ada' : 'tidak'),
            // dokumen pendukung
            $pendaftaran->dokumenPendukung ? ($pendaftaran->dokumenPendukung->file_transkrip_prestasi ? 'ada' : 'tidak') : null,
        ];
    }

    /**
     * Define the chunk size for reading data
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 500;
    }

}
