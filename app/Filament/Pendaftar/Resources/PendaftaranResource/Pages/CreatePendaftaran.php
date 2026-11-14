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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Support\Facades\DB;

class CreatePendaftaran extends CreateRecord
{
    use HasWizard;

    protected static string $resource = PendaftaranResource::class;

    protected static ?string $title = 'Form Pendaftaran Siswa Baru';

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
                                            TextInput::make('data_pribadi.nama_lengkap')
                                                ->label('Nama Lengkap Pendaftar')
                                                ->required()
                                                ->maxLength(255)
                                                ->default(fn () => auth()->user()?->name)
                                                ->placeholder('Sesuai Ijazah')
                                                ->helperText('Tulis nama lengkap sesuai ijazah'),

                                            Select::make('data_pribadi.jenis_kelamin')
                                                ->label('Jenis Kelamin')
                                                ->required()
                                                ->options([
                                                    'Laki-laki' => 'Laki-laki',
                                                    'Perempuan' => 'Perempuan',
                                                ]),

                                            TextInput::make('data_pribadi.tempat_lahir')
                                                ->label('Tempat Lahir')
                                                ->required()
                                                ->maxLength(100)
                                                ->placeholder('Kota/Kabupaten'),

                                            DatePicker::make('data_pribadi.tanggal_lahir')
                                                ->label('Tanggal Lahir')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d/m/Y')
                                                ->maxDate(now()->subYears(10))
                                                ->helperText('Format: Tanggal/Bulan/Tahun'),

                                            Select::make('data_pribadi.agama')
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

                                            Select::make('data_pribadi.golongan_darah')
                                                ->label('Golongan Darah')
                                                ->required()
                                                ->options([
                                                    'A' => 'A',
                                                    'B' => 'B',
                                                    'O' => 'O',
                                                    'AB' => 'AB',
                                                    'Belum Tahu' => 'Belum Tahu',
                                                ]),

                                            TextInput::make('data_pribadi.tinggi_badan')
                                                ->label('Tinggi Badan (cm)')
                                                ->required()
                                                ->numeric()
                                                ->suffix('cm')
                                                ->minValue(100)
                                                ->maxValue(250),

                                            TextInput::make('data_pribadi.berat_badan')
                                                ->label('Berat Badan (kg)')
                                                ->required()
                                                ->numeric()
                                                ->suffix('kg')
                                                ->minValue(20)
                                                ->maxValue(200),

                                            Select::make('data_pribadi.status_dalam_keluarga')
                                                ->label('Status dalam Keluarga')
                                                ->required()
                                                ->options([
                                                    'Anak Kandung' => 'Anak Kandung',
                                                    'Anak Tiri' => 'Anak Tiri',
                                                    'Anak Angkat' => 'Anak Angkat',
                                                ]),

                                            TextInput::make('data_pribadi.hobi')
                                                ->label('Hobi')
                                                ->maxLength(255)
                                                ->placeholder('Misal: Membaca, Olahraga, dll'),
                                        ]),

                                    Textarea::make('data_pribadi.riwayat_penyakit')
                                        ->label('Riwayat Penyakit/Kondisi Kesehatan')
                                        ->rows(3)
                                        ->placeholder('Tulis jika ada riwayat penyakit atau kondisi kesehatan khusus')
                                        ->helperText('Kosongkan jika tidak ada'),
                                ]),

                            Section::make('Alamat Lengkap')
                                ->description('Sesuai Kartu Keluarga (KK)')
                                ->schema([
                                    Textarea::make('data_pribadi.alamat_lengkap')
                                        ->label('Alamat Lengkap')
                                        ->required()
                                        ->rows(3)
                                        ->placeholder('Jalan, RT/RW, Nomor Rumah')
                                        ->helperText('Tulis alamat sesuai KK'),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('data_pribadi.desa_kelurahan')
                                                ->label('Desa/Kelurahan')
                                                ->required()
                                                ->maxLength(100),

                                            TextInput::make('data_pribadi.kecamatan')
                                                ->label('Kecamatan')
                                                ->required()
                                                ->maxLength(100),

                                            TextInput::make('data_pribadi.kota_kabupaten')
                                                ->label('Kota/Kabupaten')
                                                ->required()
                                                ->maxLength(100),

                                            TextInput::make('data_pribadi.provinsi')
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
                                            TextInput::make('data_pribadi.no_hp')
                                                ->label('No. HP/WhatsApp Aktif')
                                                ->required()
                                                ->tel()
                                                ->prefix('+62')
                                                ->maxLength(15)
                                                ->helperText('Nomor yang bisa dihubungi'),

                                            TextInput::make('data_pribadi.pilihan_jurusan')
                                                ->label('Pilihan Jurusan (Setelah Lulus SMA)')
                                                ->maxLength(100)
                                                ->placeholder('Misal: IPA, IPS, Bahasa')
                                                ->helperText('Jurusan yang diminati'),
                                        ]),

                                    FileUpload::make('data_pribadi.pas_foto')
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
                                            TextInput::make('data_orang_tua.nama_ayah')
                                                ->label('Nama Ayah')
                                                ->required()
                                                ->maxLength(255),

                                            Select::make('data_orang_tua.status_ayah')
                                                ->label('Status Ayah')
                                                ->required()
                                                ->options([
                                                    'Masih Hidup' => 'Masih Hidup',
                                                    'Sudah Meninggal' => 'Sudah Meninggal',
                                                ])
                                                ->reactive(),

                                            Select::make('data_orang_tua.pendidikan_ayah')
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

                                            Select::make('data_orang_tua.pekerjaan_ayah')
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

                                            Select::make('data_orang_tua.penghasilan_ayah')
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
                                            TextInput::make('data_orang_tua.nama_ibu')
                                                ->label('Nama Ibu')
                                                ->required()
                                                ->maxLength(255),

                                            Select::make('data_orang_tua.status_ibu')
                                                ->label('Status Ibu')
                                                ->required()
                                                ->options([
                                                    'Masih Hidup' => 'Masih Hidup',
                                                    'Sudah Meninggal' => 'Sudah Meninggal',
                                                ])
                                                ->reactive(),

                                            Select::make('data_orang_tua.pendidikan_ibu')
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

                                            Select::make('data_orang_tua.pekerjaan_ibu')
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

                                            Select::make('data_orang_tua.penghasilan_ibu')
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
                                    TextInput::make('data_orang_tua.no_hp_ortu')
                                        ->label('No. HP/WhatsApp Orang Tua')
                                        ->required()
                                        ->tel()
                                        ->prefix('+62')
                                        ->maxLength(15)
                                        ->helperText('Nomor yang bisa dihubungi'),

                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('data_orang_tua.nama_wali')
                                                ->label('Nama Wali (Jika Ada)')
                                                ->maxLength(255),

                                            Select::make('data_orang_tua.pendidikan_wali')
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

                                            TextInput::make('data_orang_tua.pekerjaan_wali')
                                                ->label('Pekerjaan Wali')
                                                ->maxLength(100),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('data_orang_tua.total_pemasukan')
                                                ->label('Total Pemasukan Orang Tua per Bulan')
                                                ->required()
                                                ->numeric()
                                                ->prefix('Rp')
                                                ->minValue(0)
                                                ->helperText('Jumlah penghasilan gabungan'),

                                            TextInput::make('data_orang_tua.total_pengeluaran')
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
                                    FileUpload::make('data_orang_tua.file_kk_ktp')
                                        ->label('Kartu Keluarga (KK) + KTP Orang Tua')
                                        ->required()
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(5120)
                                        ->directory('dokumen-ortu')
                                        ->helperText('Gabungkan KK dan KTP dalam 1 file PDF (maksimal 5MB)')
                                        ->downloadable()
                                        ->previewable(),

                                    FileUpload::make('data_orang_tua.file_ktp_wali')
                                        ->label('KTP Wali (Jika Ada)')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(1024)
                                        ->directory('dokumen-ortu')
                                        ->helperText('Format PDF, maksimal 1MB')
                                        ->downloadable()
                                        ->previewable(),

                                    FileUpload::make('data_orang_tua.file_slip_gaji')
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
                                            Select::make('data_sekolah_asal.jenjang_sekolah')
                                                ->label('Jenjang Sekolah Asal')
                                                ->required()
                                                ->options([
                                                    'SMP' => 'SMP',
                                                    'MTs' => 'MTs',
                                                ]),

                                            TextInput::make('data_sekolah_asal.nama_sekolah')
                                                ->label('Nama Sekolah')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Contoh: SMPN 1 Jakarta'),

                                            Select::make('data_sekolah_asal.status_sekolah')
                                                ->label('Status Sekolah')
                                                ->required()
                                                ->options([
                                                    'Negeri' => 'Negeri',
                                                    'Swasta' => 'Swasta',
                                                ]),
                                        ]),

                                    Textarea::make('data_sekolah_asal.alamat_sekolah')
                                        ->label('Alamat Lengkap Sekolah')
                                        ->required()
                                        ->rows(3)
                                        ->placeholder('Jalan, Kecamatan, Kota/Kabupaten'),

                                    FileUpload::make('data_sekolah_asal.file_transkrip_nilai')
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

                    // STEP 4: NILAI RAPORT
                    Wizard\Step::make('Nilai Raport')
                        ->icon('heroicon-o-document-check')
                        ->description('Input nilai raport semester 1-5')
                        ->schema([
                            Section::make('Nilai Raport Semester 1-5')
                                ->description('Masukkan nilai untuk setiap mata pelajaran per semester')
                                ->schema([
                                    Repeater::make('nilai_raport')
                                        ->relationship('nilaiRaport')
                                        ->schema([
                                            Select::make('semester')
                                                ->label('Semester')
                                                ->required()
                                                ->options([
                                                    1 => 'Semester 1',
                                                    2 => 'Semester 2',
                                                    3 => 'Semester 3',
                                                    4 => 'Semester 4',
                                                    5 => 'Semester 5',
                                                ])
                                                ->distinct()
                                                ->columnSpan(2),

                                            Grid::make(2)
                                                ->schema([
                                                    TextInput::make('nilai_agama')
                                                        ->label('Pendidikan Agama')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),

                                                    TextInput::make('nilai_ppkn')
                                                        ->label('PPKN')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),

                                                    TextInput::make('nilai_bahasa_indonesia')
                                                        ->label('Bahasa Indonesia')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),

                                                    TextInput::make('nilai_matematika')
                                                        ->label('Matematika')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),

                                                    TextInput::make('nilai_ipa')
                                                        ->label('IPA')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),

                                                    TextInput::make('nilai_ips')
                                                        ->label('IPS')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),

                                                    TextInput::make('nilai_bahasa_inggris')
                                                        ->label('Bahasa Inggris')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),

                                                    TextInput::make('nilai_seni_budaya')
                                                        ->label('Seni Budaya')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),

                                                    TextInput::make('nilai_pjok')
                                                        ->label('PJOK')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),

                                                    TextInput::make('nilai_prakarya')
                                                        ->label('Prakarya/TI')
                                                        ->required()
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->step(0.01),
                                                ]),
                                        ])
                                        ->defaultItems(5)
                                        ->minItems(5)
                                        ->maxItems(5)
                                        ->addActionLabel('Tambah Semester')
                                        ->reorderable(false)
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string => 'Semester ' . ($state['semester'] ?? '?'))
                                        ->columnSpanFull()
                                        ->helperText('Anda wajib mengisi nilai untuk 5 semester. Rata-rata akan dihitung otomatis.'),
                                ]),
                        ])->columns(1),

                    // STEP 5: PRESTASI & ORGANISASI
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
                                    FileUpload::make('dokumen_pendukung.file_transkrip_prestasi')
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
                        ->description('Periksa kembali data sebelum submit')
                        ->schema([
                            Section::make('Informasi Penting')
                                ->description('Harap dibaca sebelum submit')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('info_submit')
                                        ->content('
                                            **Pastikan semua data sudah benar!**
                                            
                                            - Periksa kembali semua data yang telah Anda isi
                                            - Pastikan semua dokumen sudah terupload dengan benar
                                            - Setelah submit, data akan masuk ke sistem verifikasi
                                            - Anda masih dapat mengedit data jika status masih DRAFT
                                            - Jika ada data yang perlu diperbaiki, verifikator akan memberi catatan
                                            
                                            **Klik "Buat" untuk menyimpan data Anda.**
                                        '),
                                ]),

                            Section::make('Ringkasan Data')
                                ->description('Data yang telah Anda isi')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('summary')
                                        ->label('')
                                        ->content(function ($get) {
                                            $nama = $get('data_pribadi.nama_lengkap') ?? '-';
                                            $nik = auth()->user()->nik ?? '-';
                                            $email = auth()->user()->email ?? '-';
                                            $sekolah = $get('data_sekolah_asal.nama_sekolah') ?? '-';
                                            
                                            return "
                                                **Data Pendaftar:**
                                                - Nama: {$nama}
                                                - NIK: {$nik}
                                                - Email: {$email}
                                                - Sekolah Asal: {$sekolah}
                                                
                                                Pastikan semua data sudah sesuai sebelum submit.
                                            ";
                                        }),
                                ]),
                        ])->columns(1),
                ])
                    ->columnSpanFull()
                    ->submitAction(view('filament.pages.actions.wizard-submit-button'))
                    ->skippable(false)
                    ->persistStepInQueryString(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'draft';
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $pendaftaran = $this->record;

        // Save Data Pribadi
        if (isset($this->data['data_pribadi'])) {
            DataPribadi::create([
                'pendaftaran_id' => $pendaftaran->id,
                ...$this->data['data_pribadi']
            ]);
        }

        // Save Data Orang Tua
        if (isset($this->data['data_orang_tua'])) {
            DataOrangTua::create([
                'pendaftaran_id' => $pendaftaran->id,
                ...$this->data['data_orang_tua']
            ]);
        }

        // Save Data Sekolah Asal
        if (isset($this->data['data_sekolah_asal'])) {
            DataSekolahAsal::create([
                'pendaftaran_id' => $pendaftaran->id,
                ...$this->data['data_sekolah_asal']
            ]);
        }

        // Save Organisasi
        if (isset($this->data['organisasi'])) {
            Organisasi::create([
                'pendaftaran_id' => $pendaftaran->id,
                ...$this->data['organisasi']
            ]);
        }

        // Save Dokumen Pendukung
        if (isset($this->data['dokumen_pendukung'])) {
            DokumenPendukung::create([
                'pendaftaran_id' => $pendaftaran->id,
                ...$this->data['dokumen_pendukung']
            ]);
        }

        Notification::make()
            ->title('Pendaftaran Berhasil Disimpan!')
            ->success()
            ->body('Data pendaftaran Anda telah tersimpan. Lengkapi dan submit untuk verifikasi.')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}