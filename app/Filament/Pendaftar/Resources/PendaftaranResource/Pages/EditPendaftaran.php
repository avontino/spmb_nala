<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use App\Models\DataOrangTua;
use App\Models\DataPribadi;
use App\Models\DataSekolahAsal;
use App\Models\DokumenPendukung;
use App\Models\NilaiRaport;
use App\Models\Organisasi;
use App\Models\Prestasi;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\HasWizard;

class EditPendaftaran extends EditRecord
{
    use HasWizard;

    protected static string $resource = PendaftaranResource::class;

    protected static ?string $title = 'Edit Form Pendaftaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // STEP 1: DATA PRIBADI
                    Wizard\Step::make('Data Pribadi')
                        ->icon('heroicon-o-user')
                        ->description('Lengkapi data pribadi pendaftar')
                        ->schema([
                            Section::make('Identitas Diri')
                                ->description('Pastikan data sesuai dengan Kartu Keluarga (KK)')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('dataPribadi.nama_lengkap')
                                                ->label('Nama Lengkap Pendaftar')
                                                ->required()
                                                ->maxLength(255)
                                                ->default(fn ($record) => $record?->user?->name)
                                                ->placeholder('Sesuai Ijazah')
                                                ->helperText('Tulis nama lengkap sesuai ijazah'),

                                            Select::make('dataPribadi.jenis_kelamin')
                                                ->label('Jenis Kelamin')
                                                ->required()
                                                ->options([
                                                    'Laki-laki' => 'Laki-laki',
                                                    'Perempuan' => 'Perempuan',
                                                ]),

                                            TextInput::make('dataPribadi.tempat_lahir')
                                                ->label('Tempat Lahir')
                                                ->required()
                                                ->maxLength(100)
                                                ->placeholder('Kota/Kabupaten'),

                                            DatePicker::make('dataPribadi.tanggal_lahir')
                                                ->label('Tanggal Lahir')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->maxDate(now()->subYears(10))
                                                ->helperText('Format: Tanggal/Bulan/Tahun'),

                                            Select::make('dataPribadi.agama')
                                                ->label('Agama')
                                                ->required()
                                                ->options([
                                                    'Islam' => 'Islam',
                                                    'Kristen Katolik' => 'Kristen Katolik',
                                                    'Kristen Protestan' => 'Kristen Protestan',
                                                    'Hindu' => 'Hindu',
                                                    'Budha' => 'Budha',
                                                    'Kong Hu Chu' => 'Kong Hu Chu',
                                                ]),

                                            Select::make('dataPribadi.golongan_darah')
                                                ->label('Golongan Darah')
                                                ->required()
                                                ->options([
                                                    'A' => 'A',
                                                    'B' => 'B',
                                                    'O' => 'O',
                                                    'AB' => 'AB',
                                                    'Belum Tahu' => 'Belum Tahu',
                                                ]),

                                            TextInput::make('dataPribadi.tinggi_badan')
                                                ->label('Tinggi Badan (cm)')
                                                ->required()
                                                ->numeric()
                                                ->suffix('cm')
                                                ->minValue(100)
                                                ->maxValue(250),

                                            TextInput::make('dataPribadi.berat_badan')
                                                ->label('Berat Badan (kg)')
                                                ->required()
                                                ->numeric()
                                                ->suffix('kg')
                                                ->minValue(20)
                                                ->maxValue(200),

                                            Select::make('dataPribadi.status_dalam_keluarga')
                                                ->label('Status dalam Keluarga')
                                                ->required()
                                                ->options([
                                                    'Anak Kandung' => 'Anak Kandung',
                                                    'Anak Tiri' => 'Anak Tiri',
                                                    'Anak Angkat' => 'Anak Angkat',
                                                ]),

                                            TextInput::make('dataPribadi.hobi')
                                                ->label('Hobi')
                                                ->maxLength(255)
                                                ->placeholder('Misal: Membaca, Olahraga, dll'),
                                        ]),

                                    Textarea::make('dataPribadi.riwayat_penyakit')
                                        ->label('Riwayat Penyakit/Kondisi Kesehatan')
                                        ->rows(3)
                                        ->placeholder('Tulis jika ada riwayat penyakit atau kondisi kesehatan khusus')
                                        ->helperText('Kosongkan jika tidak ada'),
                                ]),

                            Section::make('Alamat Lengkap')
                                ->description('Sesuai Kartu Keluarga (KK)')
                                ->schema([
                                    Textarea::make('dataPribadi.alamat_lengkap')
                                        ->label('Alamat Lengkap')
                                        ->required()
                                        ->rows(3)
                                        ->placeholder('Jalan, RT/RW, Nomor Rumah')
                                        ->helperText('Tulis alamat sesuai KK'),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('dataPribadi.desa_kelurahan')
                                                ->label('Desa/Kelurahan')
                                                ->required()
                                                ->maxLength(100),

                                            TextInput::make('dataPribadi.kecamatan')
                                                ->label('Kecamatan')
                                                ->required()
                                                ->maxLength(100),

                                            TextInput::make('dataPribadi.kota_kabupaten')
                                                ->label('Kota/Kabupaten')
                                                ->required()
                                                ->maxLength(100),

                                            TextInput::make('dataPribadi.provinsi')
                                                ->label('Provinsi Asal')
                                                ->required()
                                                ->maxLength(100)
                                                ->helperText('Provinsi sesuai KK'),
                                        ]),
                                ]),

                            Section::make('Kontak & Pendidikan')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('dataPribadi.no_hp')
                                                ->label('No. HP/WhatsApp Aktif')
                                                ->required()
                                                ->tel()
                                                ->prefix('+62')
                                                ->maxLength(15)
                                                ->helperText('Nomor yang bisa dihubungi'),

                                            TextInput::make('dataPribadi.pilihan_jurusan')
                                                ->label('Pilihan Jurusan (Setelah Lulus SMA)')
                                                ->maxLength(100)
                                                ->placeholder('Misal: IPA, IPS, Bahasa')
                                                ->helperText('Jurusan yang diminati'),
                                        ]),

                                    FileUpload::make('dataPribadi.pas_foto')
                                        ->label('Pas Foto (3x4)')
                                        ->required()
                                        ->image()
                                        ->maxSize(300)
                                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                                        ->directory('pas-foto')
                                        ->helperText('Upload foto 3x4, maksimal 300KB (JPG/JPEG/PNG)')
                                        ->downloadable()
                                        ->previewable(),
                                ]),
                        ])->columns(1),

                    // STEP 2: DATA ORANG TUA
                    Wizard\Step::make('Data Orang Tua')
                        ->icon('heroicon-o-users')
                        ->description('Lengkapi data orang tua/wali')
                        ->schema([
                            Section::make('Data Ayah')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('dataOrangTua.nama_ayah')
                                                ->label('Nama Ayah')
                                                ->required()
                                                ->maxLength(255),

                                            Select::make('dataOrangTua.status_ayah')
                                                ->label('Status Ayah')
                                                ->required()
                                                ->options([
                                                    'Masih Hidup' => 'Masih Hidup',
                                                    'Sudah Meninggal' => 'Sudah Meninggal',
                                                ])
                                                ->reactive(),

                                            Select::make('dataOrangTua.pendidikan_ayah')
                                                ->label('Pendidikan Terakhir Ayah')
                                                ->required()
                                                ->options([
                                                    'Tidak Sekolah' => 'Tidak Sekolah',
                                                    'SD/MI' => 'SD/MI',
                                                    'SMP/MTs' => 'SMP/MTs',
                                                    'SMA/SMK/MA' => 'SMA/SMK/MA',
                                                    'Diploma' => 'Diploma',
                                                    'S1' => 'S1',
                                                    'S2' => 'S2',
                                                    'S3' => 'S3',
                                                ]),

                                            Select::make('dataOrangTua.pekerjaan_ayah')
                                                ->label('Pekerjaan Ayah')
                                                ->required()
                                                ->options([
                                                    'Buruh' => 'Buruh',
                                                    'Petani' => 'Petani',
                                                    'Wiraswasta' => 'Wiraswasta',
                                                    'PNS' => 'PNS',
                                                    'TNI AD' => 'TNI AD',
                                                    'TNI AL' => 'TNI AL',
                                                    'TNI AU' => 'TNI AU',
                                                    'Polri' => 'Polri',
                                                    'Perangkat Desa' => 'Perangkat Desa',
                                                    'Nelayan' => 'Nelayan',
                                                    'Lainnya' => 'Lainnya',
                                                ]),

                                            Select::make('dataOrangTua.penghasilan_ayah')
                                                ->label('Penghasilan Ayah per Bulan')
                                                ->required()
                                                ->options([
                                                    'Kurang dari Rp 1.000.000' => 'Kurang dari Rp 1.000.000',
                                                    'Rp 1.000.000 - Rp 2.500.000' => 'Rp 1.000.000 - Rp 2.500.000',
                                                    'Rp 2.500.000 - Rp 5.000.000' => 'Rp 2.500.000 - Rp 5.000.000',
                                                    'Rp 5.000.000 - Rp 10.000.000' => 'Rp 5.000.000 - Rp 10.000.000',
                                                    'Lebih dari Rp 10.000.000' => 'Lebih dari Rp 10.000.000',
                                                ]),
                                        ]),
                                ]),

                            Section::make('Data Ibu')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('dataOrangTua.nama_ibu')
                                                ->label('Nama Ibu')
                                                ->required()
                                                ->maxLength(255),

                                            Select::make('dataOrangTua.status_ibu')
                                                ->label('Status Ibu')
                                                ->required()
                                                ->options([
                                                    'Masih Hidup' => 'Masih Hidup',
                                                    'Sudah Meninggal' => 'Sudah Meninggal',
                                                ])
                                                ->reactive(),

                                            Select::make('dataOrangTua.pendidikan_ibu')
                                                ->label('Pendidikan Terakhir Ibu')
                                                ->required()
                                                ->options([
                                                    'Tidak Sekolah' => 'Tidak Sekolah',
                                                    'SD/MI' => 'SD/MI',
                                                    'SMP/MTs' => 'SMP/MTs',
                                                    'SMA/SMK/MA' => 'SMA/SMK/MA',
                                                    'Diploma' => 'Diploma',
                                                    'S1' => 'S1',
                                                    'S2' => 'S2',
                                                    'S3' => 'S3',
                                                ]),

                                            Select::make('dataOrangTua.pekerjaan_ibu')
                                                ->label('Pekerjaan Ibu')
                                                ->required()
                                                ->options([
                                                    'Ibu Rumah Tangga' => 'Ibu Rumah Tangga',
                                                    'Buruh' => 'Buruh',
                                                    'Petani' => 'Petani',
                                                    'Wiraswasta' => 'Wiraswasta',
                                                    'PNS' => 'PNS',
                                                    'TNI AD' => 'TNI AD',
                                                    'TNI AL' => 'TNI AL',
                                                    'TNI AU' => 'TNI AU',
                                                    'Polri' => 'Polri',
                                                    'Perangkat Desa' => 'Perangkat Desa',
                                                    'Nelayan' => 'Nelayan',
                                                    'Lainnya' => 'Lainnya',
                                                ]),

                                            Select::make('dataOrangTua.penghasilan_ibu')
                                                ->label('Penghasilan Ibu per Bulan')
                                                ->required()
                                                ->options([
                                                    'Tidak Berpenghasilan' => 'Tidak Berpenghasilan',
                                                    'Kurang dari Rp 1.000.000' => 'Kurang dari Rp 1.000.000',
                                                    'Rp 1.000.000 - Rp 2.500.000' => 'Rp 1.000.000 - Rp 2.500.000',
                                                    'Rp 2.500.000 - Rp 5.000.000' => 'Rp 2.500.000 - Rp 5.000.000',
                                                    'Rp 5.000.000 - Rp 10.000.000' => 'Rp 5.000.000 - Rp 10.000.000',
                                                    'Lebih dari Rp 10.000.000' => 'Lebih dari Rp 10.000.000',
                                                ]),
                                        ]),
                                ]),

                            Section::make('Data Wali & Kontak')
                                ->description('Isi jika memiliki wali')
                                ->schema([
                                    TextInput::make('dataOrangTua.no_hp_ortu')
                                        ->label('No. HP/WhatsApp Orang Tua')
                                        ->required()
                                        ->tel()
                                        ->prefix('+62')
                                        ->maxLength(15)
                                        ->helperText('Nomor yang bisa dihubungi'),

                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('dataOrangTua.nama_wali')
                                                ->label('Nama Wali (Jika Ada)')
                                                ->maxLength(255),

                                            Select::make('dataOrangTua.pendidikan_wali')
                                                ->label('Pendidikan Wali')
                                                ->options([
                                                    'Tidak Sekolah' => 'Tidak Sekolah',
                                                    'SD/MI' => 'SD/MI',
                                                    'SMP/MTs' => 'SMP/MTs',
                                                    'SMA/SMK/MA' => 'SMA/SMK/MA',
                                                    'Diploma' => 'Diploma',
                                                    'S1' => 'S1',
                                                    'S2' => 'S2',
                                                    'S3' => 'S3',
                                                ]),

                                            TextInput::make('dataOrangTua.pekerjaan_wali')
                                                ->label('Pekerjaan Wali')
                                                ->maxLength(100),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('dataOrangTua.total_pemasukan')
                                                ->label('Total Pemasukan Orang Tua per Bulan')
                                                ->required()
                                                ->numeric()
                                                ->prefix('Rp')
                                                ->minValue(0)
                                                ->helperText('Jumlah penghasilan gabungan'),

                                            TextInput::make('dataOrangTua.total_pengeluaran')
                                                ->label('Total Pengeluaran Orang Tua per Bulan')
                                                ->required()
                                                ->numeric()
                                                ->prefix('Rp')
                                                ->minValue(0)
                                                ->helperText('Estimasi pengeluaran bulanan'),
                                        ]),
                                ]),

                            Section::make('Dokumen Orang Tua')
                                ->description('Upload dokumen yang diperlukan')
                                ->schema([
                                    FileUpload::make('dataOrangTua.file_kk_ktp')
                                        ->label('Kartu Keluarga (KK) + KTP Orang Tua')
                                        ->required()
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(5120)
                                        ->directory('dokumen-ortu')
                                        ->helperText('Gabungkan KK dan KTP dalam 1 file PDF (maksimal 5MB)')
                                        ->downloadable()
                                        ->previewable(),

                                    FileUpload::make('dataOrangTua.file_ktp_wali')
                                        ->label('KTP Wali (Jika Ada)')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(1024)
                                        ->directory('dokumen-ortu')
                                        ->helperText('Format PDF, maksimal 1MB')
                                        ->downloadable()
                                        ->previewable(),

                                    FileUpload::make('dataOrangTua.file_slip_gaji')
                                        ->label('Slip Gaji / Surat Keterangan Penghasilan')
                                        ->required()
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(5120)
                                        ->directory('dokumen-ortu')
                                        ->helperText('Format PDF, maksimal 5MB')
                                        ->downloadable()
                                        ->previewable(),
                                ]),
                        ])->columns(1),

                    // STEP 3: DATA SEKOLAH ASAL
                    Wizard\Step::make('Data Sekolah Asal')
                        ->icon('heroicon-o-academic-cap')
                        ->description('Lengkapi data sekolah asal')
                        ->schema([
                            Section::make('Informasi Sekolah Asal')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Select::make('dataSekolahAsal.jenjang_sekolah')
                                                ->label('Jenjang Sekolah Asal')
                                                ->required()
                                                ->options([
                                                    'SMP' => 'SMP',
                                                    'MTs' => 'MTs',
                                                ]),

                                            TextInput::make('dataSekolahAsal.nama_sekolah')
                                                ->label('Nama Sekolah')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Contoh: SMPN 1 Jakarta'),

                                            Select::make('dataSekolahAsal.status_sekolah')
                                                ->label('Status Sekolah')
                                                ->required()
                                                ->options([
                                                    'Negeri' => 'Negeri',
                                                    'Swasta' => 'Swasta',
                                                ]),
                                        ]),

                                    Textarea::make('dataSekolahAsal.alamat_sekolah')
                                        ->label('Alamat Lengkap Sekolah')
                                        ->required()
                                        ->rows(3)
                                        ->placeholder('Jalan, Kecamatan, Kota/Kabupaten'),

                                    FileUpload::make('dataSekolahAsal.file_transkrip_nilai')
                                        ->label('Transkrip Nilai Raport (Semester 1-5)')
                                        ->required()
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(5120)
                                        ->directory('transkrip-nilai')
                                        ->helperText('Upload transkrip nilai semester 1-5 dalam 1 file PDF (maksimal 5MB)')
                                        ->downloadable()
                                        ->previewable(),
                                ]),
                        ])->columns(1),

                    // STEP 4: NILAI RAPORT (IMPROVED VERSION)
                    Wizard\Step::make('Nilai Raport')
                        ->icon('heroicon-o-document-check')
                        ->description('Input nilai raport semester 1-5')
                        ->schema([
                            Section::make('Nilai Raport Semester 1-5')
                                ->description('Masukkan nilai untuk setiap mata pelajaran per semester. Rata-rata akan dihitung otomatis.')
                                ->schema([
                                    // SEMESTER 1
                                    Section::make('📚 Semester 1')
                                        ->schema([
                                            Grid::make(5)
                                                ->schema([
                                                    TextInput::make('nilaiRaport.0.semester')
                                                        ->default(1)
                                                        ->hidden()
                                                        ->dehydrated(),

                                                    TextInput::make('nilaiRaport.0.nilai_agama')
                                                        ->label('Agama')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.0.nilai_ppkn')
                                                        ->label('PPKN')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.0.nilai_bahasa_indonesia')
                                                        ->label('B. Indonesia')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.0.nilai_matematika')
                                                        ->label('Matematika')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.0.nilai_ipa')
                                                        ->label('IPA')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.0.nilai_ips')
                                                        ->label('IPS')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.0.nilai_bahasa_inggris')
                                                        ->label('B. Inggris')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.0.nilai_seni_budaya')
                                                        ->label('Seni Budaya')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.0.nilai_pjok')
                                                        ->label('PJOK')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.0.nilai_prakarya')
                                                        ->label('Prakarya/TI')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),
                                                ]),

                                            Placeholder::make('rata_sem1')
                                                ->label('Rata-rata Semester 1')
                                                ->content(function (Get $get): string {
                                                    $nilai = [
                                                        $get('nilaiRaport.0.nilai_agama') ?? 0,
                                                        $get('nilaiRaport.0.nilai_ppkn') ?? 0,
                                                        $get('nilaiRaport.0.nilai_bahasa_indonesia') ?? 0,
                                                        $get('nilaiRaport.0.nilai_matematika') ?? 0,
                                                        $get('nilaiRaport.0.nilai_ipa') ?? 0,
                                                        $get('nilaiRaport.0.nilai_ips') ?? 0,
                                                        $get('nilaiRaport.0.nilai_bahasa_inggris') ?? 0,
                                                        $get('nilaiRaport.0.nilai_seni_budaya') ?? 0,
                                                        $get('nilaiRaport.0.nilai_pjok') ?? 0,
                                                        $get('nilaiRaport.0.nilai_prakarya') ?? 0,
                                                    ];
                                                    
                                                    $validNilai = array_filter($nilai, fn($n) => $n > 0);
                                                    $avg = count($validNilai) > 0 ? array_sum($validNilai) / count($validNilai) : 0;
                                                    
                                                    return '🎯 **' . number_format($avg, 2) . '**';
                                                }),
                                        ])
                                        ->collapsible()
                                        ->collapsed(false),

                                    // SEMESTER 2
                                    Section::make('📚 Semester 2')
                                        ->schema([
                                            Grid::make(5)
                                                ->schema([
                                                    TextInput::make('nilaiRaport.1.semester')
                                                        ->default(2)
                                                        ->hidden()
                                                        ->dehydrated(),

                                                    TextInput::make('nilaiRaport.1.nilai_agama')
                                                        ->label('Agama')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.1.nilai_ppkn')
                                                        ->label('PPKN')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.1.nilai_bahasa_indonesia')
                                                        ->label('B. Indonesia')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.1.nilai_matematika')
                                                        ->label('Matematika')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.1.nilai_ipa')
                                                        ->label('IPA')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.1.nilai_ips')
                                                        ->label('IPS')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.1.nilai_bahasa_inggris')
                                                        ->label('B. Inggris')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.1.nilai_seni_budaya')
                                                        ->label('Seni Budaya')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.1.nilai_pjok')
                                                        ->label('PJOK')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.1.nilai_prakarya')
                                                        ->label('Prakarya/TI')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),
                                                ]),

                                            Placeholder::make('rata_sem2')
                                                ->label('Rata-rata Semester 2')
                                                ->content(function (Get $get): string {
                                                    $nilai = [
                                                        $get('nilaiRaport.1.nilai_agama') ?? 0,
                                                        $get('nilaiRaport.1.nilai_ppkn') ?? 0,
                                                        $get('nilaiRaport.1.nilai_bahasa_indonesia') ?? 0,
                                                        $get('nilaiRaport.1.nilai_matematika') ?? 0,
                                                        $get('nilaiRaport.1.nilai_ipa') ?? 0,
                                                        $get('nilaiRaport.1.nilai_ips') ?? 0,
                                                        $get('nilaiRaport.1.nilai_bahasa_inggris') ?? 0,
                                                        $get('nilaiRaport.1.nilai_seni_budaya') ?? 0,
                                                        $get('nilaiRaport.1.nilai_pjok') ?? 0,
                                                        $get('nilaiRaport.1.nilai_prakarya') ?? 0,
                                                    ];
                                                    
                                                    $validNilai = array_filter($nilai, fn($n) => $n > 0);
                                                    $avg = count($validNilai) > 0 ? array_sum($validNilai) / count($validNilai) : 0;
                                                    
                                                    return '🎯 **' . number_format($avg, 2) . '**';
                                                }),
                                        ])
                                        ->collapsible()
                                        ->collapsed(true),

                                    // SEMESTER 3
                                    Section::make('📚 Semester 3')
                                        ->schema([
                                            Grid::make(5)
                                                ->schema([
                                                    TextInput::make('nilaiRaport.2.semester')
                                                        ->default(3)
                                                        ->hidden()
                                                        ->dehydrated(),

                                                    TextInput::make('nilaiRaport.2.nilai_agama')
                                                        ->label('Agama')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.2.nilai_ppkn')
                                                        ->label('PPKN')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.2.nilai_bahasa_indonesia')
                                                        ->label('B. Indonesia')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.2.nilai_matematika')
                                                        ->label('Matematika')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.2.nilai_ipa')
                                                        ->label('IPA')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.2.nilai_ips')
                                                        ->label('IPS')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.2.nilai_bahasa_inggris')
                                                        ->label('B. Inggris')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.2.nilai_seni_budaya')
                                                        ->label('Seni Budaya')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.2.nilai_pjok')
                                                        ->label('PJOK')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.2.nilai_prakarya')
                                                        ->label('Prakarya/TI')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),
                                                ]),

                                            Placeholder::make('rata_sem3')
                                                ->label('Rata-rata Semester 3')
                                                ->content(function (Get $get): string {
                                                    $nilai = [
                                                        $get('nilaiRaport.2.nilai_agama') ?? 0,
                                                        $get('nilaiRaport.2.nilai_ppkn') ?? 0,
                                                        $get('nilaiRaport.2.nilai_bahasa_indonesia') ?? 0,
                                                        $get('nilaiRaport.2.nilai_matematika') ?? 0,
                                                        $get('nilaiRaport.2.nilai_ipa') ?? 0,
                                                        $get('nilaiRaport.2.nilai_ips') ?? 0,
                                                        $get('nilaiRaport.2.nilai_bahasa_inggris') ?? 0,
                                                        $get('nilaiRaport.2.nilai_seni_budaya') ?? 0,
                                                        $get('nilaiRaport.2.nilai_pjok') ?? 0,
                                                        $get('nilaiRaport.2.nilai_prakarya') ?? 0,
                                                    ];
                                                    
                                                    $validNilai = array_filter($nilai, fn($n) => $n > 0);
                                                    $avg = count($validNilai) > 0 ? array_sum($validNilai) / count($validNilai) : 0;
                                                    
                                                    return '🎯 **' . number_format($avg, 2) . '**';
                                                }),
                                        ])
                                        ->collapsible()
                                        ->collapsed(true),

                                    // SEMESTER 4
                                    Section::make('📚 Semester 4')
                                        ->schema([
                                            Grid::make(5)
                                                ->schema([
                                                    TextInput::make('nilaiRaport.3.semester')
                                                        ->default(4)
                                                        ->hidden()
                                                        ->dehydrated(),

                                                    TextInput::make('nilaiRaport.3.nilai_agama')
                                                        ->label('Agama')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.3.nilai_ppkn')
                                                        ->label('PPKN')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.3.nilai_bahasa_indonesia')
                                                        ->label('B. Indonesia')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.3.nilai_matematika')
                                                        ->label('Matematika')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.3.nilai_ipa')
                                                        ->label('IPA')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.3.nilai_ips')
                                                        ->label('IPS')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.3.nilai_bahasa_inggris')
                                                        ->label('B. Inggris')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.3.nilai_seni_budaya')
                                                        ->label('Seni Budaya')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.3.nilai_pjok')
                                                        ->label('PJOK')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.3.nilai_prakarya')
                                                        ->label('Prakarya/TI')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),
                                                ]),

                                            Placeholder::make('rata_sem4')
                                                ->label('Rata-rata Semester 4')
                                                ->content(function (Get $get): string {
                                                    $nilai = [
                                                        $get('nilaiRaport.3.nilai_agama') ?? 0,
                                                        $get('nilaiRaport.3.nilai_ppkn') ?? 0,
                                                        $get('nilaiRaport.3.nilai_bahasa_indonesia') ?? 0,
                                                        $get('nilaiRaport.3.nilai_matematika') ?? 0,
                                                        $get('nilaiRaport.3.nilai_ipa') ?? 0,
                                                        $get('nilaiRaport.3.nilai_ips') ?? 0,
                                                        $get('nilaiRaport.3.nilai_bahasa_inggris') ?? 0,
                                                        $get('nilaiRaport.3.nilai_seni_budaya') ?? 0,
                                                        $get('nilaiRaport.3.nilai_pjok') ?? 0,
                                                        $get('nilaiRaport.3.nilai_prakarya') ?? 0,
                                                    ];
                                                    
                                                    $validNilai = array_filter($nilai, fn($n) => $n > 0);
                                                    $avg = count($validNilai) > 0 ? array_sum($validNilai) / count($validNilai) : 0;
                                                    
                                                    return '🎯 **' . number_format($avg, 2) . '**';
                                                }),
                                        ])
                                        ->collapsible()
                                        ->collapsed(true),

                                    // SEMESTER 5
                                    Section::make('📚 Semester 5')
                                        ->schema([
                                            Grid::make(5)
                                                ->schema([
                                                    TextInput::make('nilaiRaport.4.semester')
                                                        ->default(5)
                                                        ->hidden()
                                                        ->dehydrated(),

                                                    TextInput::make('nilaiRaport.4.nilai_agama')
                                                        ->label('Agama')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.4.nilai_ppkn')
                                                        ->label('PPKN')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.4.nilai_bahasa_indonesia')
                                                        ->label('B. Indonesia')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.4.nilai_matematika')
                                                        ->label('Matematika')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.4.nilai_ipa')
                                                        ->label('IPA')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.4.nilai_ips')
                                                        ->label('IPS')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.4.nilai_bahasa_inggris')
                                                        ->label('B. Inggris')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.4.nilai_seni_budaya')
                                                        ->label('Seni Budaya')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.4.nilai_pjok')
                                                        ->label('PJOK')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),

                                                    TextInput::make('nilaiRaport.4.nilai_prakarya')
                                                        ->label('Prakarya/TI')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->live(onBlur: true),
                                                ]),

                                            Placeholder::make('rata_sem5')
                                                ->label('Rata-rata Semester 5')
                                                ->content(function (Get $get): string {
                                                    $nilai = [
                                                        $get('nilaiRaport.4.nilai_agama') ?? 0,
                                                        $get('nilaiRaport.4.nilai_ppkn') ?? 0,
                                                        $get('nilaiRaport.4.nilai_bahasa_indonesia') ?? 0,
                                                        $get('nilaiRaport.4.nilai_matematika') ?? 0,
                                                        $get('nilaiRaport.4.nilai_ipa') ?? 0,
                                                        $get('nilaiRaport.4.nilai_ips') ?? 0,
                                                        $get('nilaiRaport.4.nilai_bahasa_inggris') ?? 0,
                                                        $get('nilaiRaport.4.nilai_seni_budaya') ?? 0,
                                                        $get('nilaiRaport.4.nilai_pjok') ?? 0,
                                                        $get('nilaiRaport.4.nilai_prakarya') ?? 0,
                                                    ];
                                                    
                                                    $validNilai = array_filter($nilai, fn($n) => $n > 0);
                                                    $avg = count($validNilai) > 0 ? array_sum($validNilai) / count($validNilai) : 0;
                                                    
                                                    return '🎯 **' . number_format($avg, 2) . '**';
                                                }),
                                        ])
                                        ->collapsible()
                                        ->collapsed(true),

                                    // PREVIEW RATA-RATA KESELURUHAN
                                    Section::make('📊 Ringkasan Nilai')
                                        ->schema([
                                            Placeholder::make('rata_keseluruhan')
                                                ->label('🏆 RATA-RATA KESELURUHAN (Semester 1-5)')
                                                ->content(function (Get $get): string {
                                                    $allNilai = [];
                                                    
                                                    for ($i = 0; $i < 5; $i++) {
                                                        $nilai = [
                                                            $get("nilaiRaport.{$i}.nilai_agama") ?? 0,
                                                            $get("nilaiRaport.{$i}.nilai_ppkn") ?? 0,
                                                            $get("nilaiRaport.{$i}.nilai_bahasa_indonesia") ?? 0,
                                                            $get("nilaiRaport.{$i}.nilai_matematika") ?? 0,
                                                            $get("nilaiRaport.{$i}.nilai_ipa") ?? 0,
                                                            $get("nilaiRaport.{$i}.nilai_ips") ?? 0,
                                                            $get("nilaiRaport.{$i}.nilai_bahasa_inggris") ?? 0,
                                                            $get("nilaiRaport.{$i}.nilai_seni_budaya") ?? 0,
                                                            $get("nilaiRaport.{$i}.nilai_pjok") ?? 0,
                                                            $get("nilaiRaport.{$i}.nilai_prakarya") ?? 0,
                                                        ];
                                                        
                                                        $allNilai = array_merge($allNilai, $nilai);
                                                    }
                                                    
                                                    $validNilai = array_filter($allNilai, fn($n) => $n > 0);
                                                    $avg = count($validNilai) > 0 ? array_sum($validNilai) / count($validNilai) : 0;
                                                    
                                                    return '### ' . number_format($avg, 2);
                                                }),
                                        ])
                                        ->columnSpanFull(),
                                ]),
                        ])->columns(1),

                    // STEP 5: PRESTASI & ORGANISASI (tetap sama)
                    Wizard\Step::make('Prestasi & Organisasi')
                        ->icon('heroicon-o-trophy')
                        ->description('Input prestasi dan pengalaman organisasi')
                        ->schema([
                            Section::make('Prestasi yang Diraih')
                                ->description('Maksimal 3 prestasi (kosongkan jika tidak ada)')
                                ->schema([
                                    Repeater::make('prestasi')
                                        ->relationship('prestasi')
                                        ->schema([
                                            TextInput::make('urutan')
                                                ->label('Prestasi ke-')
                                                ->required()
                                                ->numeric()
                                                ->default(fn ($get) => count($get('../../prestasi')) + 1)
                                                ->disabled(),

                                            TextInput::make('nama_prestasi')
                                                ->label('Nama Prestasi')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Contoh: Juara 1 Olimpiade Matematika')
                                                ->columnSpan(2),

                                            Select::make('tingkat_prestasi')
                                                ->label('Tingkat Prestasi')
                                                ->required()
                                                ->options([
                                                    'Kota/Kabupaten' => 'Kota/Kabupaten',
                                                    'Provinsi' => 'Provinsi',
                                                    'Nasional' => 'Nasional',
                                                    'Internasional' => 'Internasional',
                                                ]),

                                            FileUpload::make('file_bukti_prestasi')
                                                ->label('Bukti Prestasi (Sertifikat/Piagam)')
                                                ->required()
                                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                ->maxSize(2048)
                                                ->directory('bukti-prestasi')
                                                ->helperText('PDF atau Gambar, maksimal 2MB')
                                                ->downloadable()
                                                ->previewable()
                                                ->columnSpan(2),
                                        ])
                                        ->columns(3)
                                        ->defaultItems(0)
                                        ->minItems(0)
                                        ->maxItems(3)
                                        ->addActionLabel('Tambah Prestasi')
                                        ->reorderable()
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string => $state['nama_prestasi'] ?? 'Prestasi Baru')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Pengalaman Organisasi')
                                ->description('Isi jika memiliki pengalaman organisasi')
                                ->schema([
                                    TextInput::make('organisasi.nama_organisasi')
                                        ->label('Nama Organisasi')
                                        ->maxLength(255)
                                        ->placeholder('Contoh: OSIS, Pramuka, PMR'),

                                    Select::make('organisasi.jabatan')
                                        ->label('Jabatan dalam Organisasi')
                                        ->options([
                                            'Ketua' => 'Ketua',
                                            'Wakil Ketua' => 'Wakil Ketua',
                                            'Sekretaris' => 'Sekretaris',
                                            'Bendahara' => 'Bendahara',
                                            'Anggota' => 'Anggota',
                                            'Lainnya' => 'Lainnya',
                                        ]),

                                    FileUpload::make('organisasi.file_bukti_organisasi')
                                        ->label('Bukti Organisasi (SK/Sertifikat)')
                                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                                        ->maxSize(2048)
                                        ->directory('bukti-organisasi')
                                        ->helperText('PDF atau Gambar, maksimal 2MB (Opsional)')
                                        ->downloadable()
                                        ->previewable(),
                                ]),

                            Section::make('Dokumen Pendukung')
                                ->schema([
                                    FileUpload::make('dokumenPendukung.file_transkrip_prestasi')
                                        ->label('Transkrip Prestasi (dengan TTD Kepala Sekolah)')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(3072)
                                        ->directory('transkrip-prestasi')
                                        ->helperText('Upload transkrip prestasi yang sudah ditandatangani kepala sekolah (PDF, maksimal 3MB)')
                                        ->downloadable()
                                        ->previewable(),
                                ]),
                        ])->columns(1),

                    // STEP 6: REVIEW & SUBMIT
                    Wizard\Step::make('Review & Submit')
                        ->icon('heroicon-o-check-circle')
                        ->description('Periksa kembali data sebelum simpan')
                        ->schema([
                            Section::make('Informasi Penting')
                                ->description('Harap dibaca sebelum menyimpan perubahan')
                                ->schema([
                                    Placeholder::make('info_submit')
                                        ->content('
                                            **Pastikan semua data sudah benar!**
                                            
                                            - Periksa kembali semua data yang telah Anda ubah
                                            - Pastikan semua dokumen sudah terupload dengan benar
                                            - Setelah simpan, data akan diperbarui
                                            - Jika status masih DRAFT, Anda masih bisa edit lagi
                                            
                                            **Klik "Save changes" untuk menyimpan perubahan Anda.**
                                        '),
                                ]),
                        ])->columns(1),
                ])
                    ->columnSpanFull()
                    ->skippable(false)
                    ->persistStepInQueryString(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('submit')
                ->label('Submit untuk Verifikasi')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Submit Pendaftaran')
                ->modalDescription('Apakah Anda yakin ingin mengirim pendaftaran ini untuk diverifikasi? Setelah submit, data tidak dapat diubah kecuali verifikator meminta perbaikan.')
                ->modalSubmitActionLabel('Ya, Submit')
                ->visible(fn ($record) => $record->status === 'draft')
                ->action(function ($record) {
                    $record->update([
                        'status' => 'submitted',
                        'submitted_at' => now(),
                    ]);
                    
                    Notification::make()
                        ->title('Berhasil Submit!')
                        ->success()
                        ->body('Pendaftaran Anda telah dikirim untuk verifikasi.')
                        ->send();
                        
                    return redirect()->route('filament.pendaftar.resources.pendaftarans.index');
                }),
                
            Actions\DeleteAction::make()
                ->visible(fn ($record) => $record->status === 'draft'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $pendaftaran = $this->record;
        
        if ($pendaftaran->dataPribadi) {
            $data['dataPribadi'] = $pendaftaran->dataPribadi->toArray();
        }
        
        if ($pendaftaran->dataOrangTua) {
            $data['dataOrangTua'] = $pendaftaran->dataOrangTua->toArray();
        }
        
        if ($pendaftaran->dataSekolahAsal) {
            $data['dataSekolahAsal'] = $pendaftaran->dataSekolahAsal->toArray();
        }
        
        // Load nilai raport dengan index array yang benar
        if ($pendaftaran->nilaiRaport) {
            $data['nilaiRaport'] = $pendaftaran->nilaiRaport->toArray();
        }
        
        if ($pendaftaran->organisasi) {
            $data['organisasi'] = $pendaftaran->organisasi->toArray();
        }
        
        if ($pendaftaran->dokumenPendukung) {
            $data['dokumenPendukung'] = $pendaftaran->dokumenPendukung->toArray();
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $pendaftaran = $this->record;

        // Update Data Pribadi
        if (isset($this->data['dataPribadi'])) {
            $pendaftaran->dataPribadi()->updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                $this->data['dataPribadi']
            );
        }

        // Update Data Orang Tua
        if (isset($this->data['dataOrangTua'])) {
            $pendaftaran->dataOrangTua()->updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                $this->data['dataOrangTua']
            );
        }

        // Update Data Sekolah Asal
        if (isset($this->data['dataSekolahAsal'])) {
            $pendaftaran->dataSekolahAsal()->updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                $this->data['dataSekolahAsal']
            );
        }

        // Update Nilai Raport (hapus yang lama, insert yang baru)
        if (isset($this->data['nilaiRaport'])) {
            // Hapus nilai raport lama
            $pendaftaran->nilaiRaport()->delete();
            
            // Insert nilai raport baru
            foreach ($this->data['nilaiRaport'] as $nilai) {
                $pendaftaran->nilaiRaport()->create($nilai);
            }
        }

        // Update Organisasi
        if (isset($this->data['organisasi'])) {
            $pendaftaran->organisasi()->updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                $this->data['organisasi']
            );
        }

        // Update Dokumen Pendukung
        if (isset($this->data['dokumenPendukung'])) {
            $pendaftaran->dokumenPendukung()->updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                $this->data['dokumenPendukung']
            );
        }

        Notification::make()
            ->title('Perubahan Berhasil Disimpan!')
            ->success()
            ->body('Data pendaftaran Anda telah diperbarui.')
            ->send();
    }
}