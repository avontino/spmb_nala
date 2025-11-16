<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use App\Filament\Pendaftar\Resources\PendaftaranResource\Pages\Concerns\PreventEditWhenSubmitted;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditDataPribadi extends EditRecord
{
    use PreventEditWhenSubmitted;
    
    protected static string $resource = PendaftaranResource::class;
    
    // ✅ SET VERIFICATION FIELD
    protected string $verificationField = 'data_pribadi_ok';
    
    protected static ?string $title = '✏️ Edit Data Pribadi';
    protected static ?string $navigationLabel = 'Data Pribadi';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?int $navigationSort = 1;
    
    public function getBreadcrumb(): string
    {
        return 'Data Pribadi';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ✅ BANNER REVISION (MERAH)
                Section::make()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('revision_alert')
                            ->label('')
                            ->content(function () {
                                $verifikasi = $this->record->latestVerifikasi;
                                return new \Illuminate\Support\HtmlString('
                                    <div class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/20 border-2 border-red-500 rounded-lg">
                                        <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <div>
                                            <h3 class="font-semibold text-red-900 dark:text-red-100 mb-1">
                                                🔴 Bagian Ini Perlu Diperbaiki
                                            </h3>
                                            <p class="text-sm text-red-700 dark:text-red-300">
                                                ' . ($verifikasi->catatan_revisi ?? 'Silakan perbaiki data pada bagian ini sesuai catatan verifikator.') . '
                                            </p>
                                        </div>
                                    </div>
                                ');
                            }),
                    ])
                    ->visible(fn () => $this->needsRevision()),

                // ✅ BANNER READONLY (HIJAU)
                Section::make()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('readonly_alert')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900/20 border-2 border-green-500 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-green-900 dark:text-green-100 mb-1">
                                            ✅ Bagian Ini Sudah Diverifikasi
                                        </h3>
                                        <p class="text-sm text-green-700 dark:text-green-300">
                                            Data pada bagian ini sudah diverifikasi dan tidak dapat diubah.
                                        </p>
                                    </div>
                                </div>
                            ')),
                    ])
                    ->visible(fn () => $this->isFormReadonly() && !$this->needsRevision()),

Section::make('Identitas Diri')
    ->description('Pastikan data sesuai dengan Kartu Keluarga (KK)')
    ->schema([
        Grid::make(2)
            ->schema([
                TextInput::make('nama_lengkap')
                    ->label('Nama Lengkap Pendaftar')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Sesuai Ijazah')
                    ->helperText('Tulis nama lengkap sesuai ijazah'),

                Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->required()
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ]),

                TextInput::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Kota/Kabupaten'),

                DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->maxDate(now()->subYears(10))
                    ->helperText('Format: Tanggal/Bulan/Tahun'),

                Select::make('agama')
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

                Select::make('golongan_darah')
                    ->label('Golongan Darah')
                    ->required()
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'O' => 'O',
                        'AB' => 'AB',
                        'Belum Tahu' => 'Belum Tahu',
                    ]),

                TextInput::make('tinggi_badan')
                    ->label('Tinggi Badan (cm)')
                    ->required()
                    ->numeric()
                    ->suffix('cm')
                    ->minValue(100)
                    ->maxValue(250),

                TextInput::make('berat_badan')
                    ->label('Berat Badan (kg)')
                    ->required()
                    ->numeric()
                    ->suffix('kg')
                    ->minValue(20)
                    ->maxValue(200),

                Select::make('status_dalam_keluarga')
                    ->label('Status dalam Keluarga')
                    ->required()
                    ->options([
                        'Anak Kandung' => 'Anak Kandung',
                        'Anak Tiri' => 'Anak Tiri',
                        'Anak Angkat' => 'Anak Angkat',
                    ]),

                TextInput::make('hobi')
                    ->label('Hobi')
                    ->maxLength(255)
                    ->placeholder('Misal: Membaca, Olahraga, dll'),
            ]),

        Textarea::make('riwayat_penyakit')
            ->label('Riwayat Penyakit/Kondisi Kesehatan')
            ->rows(3)
            ->placeholder('Tulis jika ada riwayat penyakit atau kondisi kesehatan khusus')
            ->helperText('Kosongkan jika tidak ada'),

        // ✅ BARU: KETERANGAN ALERGI
        Grid::make(2)
            ->schema([
                Select::make('status_alergi')
                    ->label('Apakah Memiliki Alergi?')
                    ->required()
                    ->options([
                        'Tidak Ada Alergi' => 'Tidak Ada Alergi',
                        'Memiliki Alergi' => 'Memiliki Alergi',
                    ])
                    ->default('Tidak Ada Alergi')
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('keterangan_alergi', null)),

                TextInput::make('keterangan_alergi')
                    ->label('Jenis/Penyebab Alergi')
                    ->maxLength(255)
                    ->placeholder('Contoh: Alergi debu, makanan laut, obat tertentu...')
                    ->helperText('Sebutkan jenis atau penyebab alergi')
                    ->visible(fn (callable $get) => $get('status_alergi') === 'Memiliki Alergi')
                    ->required(fn (callable $get) => $get('status_alergi') === 'Memiliki Alergi'),
            ]),

        // ✅ BARU: BAHASA ASING
        TextInput::make('bahasa_asing_dikuasai')
            ->label('Bahasa Asing yang Dikuasai')
            ->maxLength(255)
            ->placeholder('Contoh: Inggris (aktif), Arab (pasif), Jepang (dasar)')
            ->helperText('Tidak wajib diisi. Tuliskan bahasa asing yang dikuasai beserta tingkat kemampuannya'),
    ]),

Section::make('Alamat Lengkap')
    ->description('Sesuai Kartu Keluarga (KK)')
    ->schema([
        Textarea::make('alamat_lengkap')
            ->label('Alamat Lengkap')
            ->required()
            ->rows(3)
            ->placeholder('Jalan, RT/RW, Nomor Rumah')
            ->helperText('Tulis alamat sesuai KK'),

        Grid::make(2)
            ->schema([
                TextInput::make('desa_kelurahan')
                    ->label('Desa/Kelurahan')
                    ->required()
                    ->maxLength(100),

                TextInput::make('kecamatan')
                    ->label('Kecamatan')
                    ->required()
                    ->maxLength(100),

                TextInput::make('kota_kabupaten')
                    ->label('Kota/Kabupaten')
                    ->required()
                    ->maxLength(100),

                Select::make('provinsi')
                    ->label('Provinsi Asal')
                    ->required()
                    ->options([
                        'Aceh' => 'Aceh',
                        'Sumatera Utara' => 'Sumatera Utara',
                        'Sumatera Barat' => 'Sumatera Barat',
                        'Riau' => 'Riau',
                        'Kepulauan Riau' => 'Kepulauan Riau',
                        'Jambi' => 'Jambi',
                        'Sumatera Selatan' => 'Sumatera Selatan',
                        'Kepulauan Bangka Belitung' => 'Kepulauan Bangka Belitung',
                        'Bengkulu' => 'Bengkulu',
                        'Lampung' => 'Lampung',
                        'DKI Jakarta' => 'DKI Jakarta',
                        'Banten' => 'Banten',
                        'Jawa Barat' => 'Jawa Barat',
                        'Jawa Tengah' => 'Jawa Tengah',
                        'DI Yogyakarta' => 'DI Yogyakarta',
                        'Jawa Timur' => 'Jawa Timur',
                        'Bali' => 'Bali',
                        'Nusa Tenggara Barat' => 'Nusa Tenggara Barat',
                        'Nusa Tenggara Timur' => 'Nusa Tenggara Timur',
                        'Kalimantan Barat' => 'Kalimantan Barat',
                        'Kalimantan Tengah' => 'Kalimantan Tengah',
                        'Kalimantan Selatan' => 'Kalimantan Selatan',
                        'Kalimantan Timur' => 'Kalimantan Timur',
                        'Kalimantan Utara' => 'Kalimantan Utara',
                        'Sulawesi Utara' => 'Sulawesi Utara',
                        'Sulawesi Barat' => 'Sulawesi Barat',
                        'Sulawesi Tengah' => 'Sulawesi Tengah',
                        'Sulawesi Tenggara' => 'Sulawesi Tenggara',
                        'Sulawesi Selatan' => 'Sulawesi Selatan',
                        'Gorontalo' => 'Gorontalo',
                        'Maluku' => 'Maluku',
                        'Maluku Utara' => 'Maluku Utara',
                        'Papua' => 'Papua',
                        'Papua Barat' => 'Papua Barat',
                        'Papua Tengah' => 'Papua Tengah',
                        'Papua Pegunungan' => 'Papua Pegunungan',
                        'Papua Selatan' => 'Papua Selatan',
                        'Papua Barat Daya' => 'Papua Barat Daya',
                    ])
                    ->searchable()
                    ->helperText('Provinsi sesuai KK'),
            ]),
    ]),

Section::make('Kontak & Rencana Studi')
    ->schema([
        Grid::make(2)
            ->schema([
                TextInput::make('no_hp')
                    ->label('No. HP/WhatsApp Aktif')
                    ->required()
                    ->tel()
                    ->prefix('+62')
                    ->maxLength(15)
                    ->helperText('Nomor yang bisa dihubungi'),

                // ✅ UPDATED: PILIHAN JURUSAN DIGANTI JADI STUDI LANJUT
                TextInput::make('pilihan_jurusan')
                    ->label('Rencana Studi Lanjut Setelah SMA')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Kedokteran, Teknik Informatika, Hukum, Sastra Inggris')
                    ->helperText('Tuliskan jurusan/program studi yang diminati setelah lulus SMA'),
            ]),

        FileUpload::make('pas_foto')
            ->label('Pas Foto (3x4)')
            ->required()
            ->image()
            ->maxSize(2048)
            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
            ->disk('public')
            ->directory('pas-foto')
            ->visibility('public')
            ->helperText('Upload foto 3x4, maksimal 2MB (JPG/JPEG/PNG)')
            ->downloadable()
            ->openable()
            ->imagePreviewHeight('250')
            ->loadingIndicatorPosition('center')
            ->panelLayout('integrated')
            ->removeUploadedFileButtonPosition('right')
            ->uploadButtonPosition('left')
            ->uploadProgressIndicatorPosition('left'),
    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Optimasi query untuk menghindari N+1 problem
        $this->record->load('dataPribadi');
        
        if ($this->record->dataPribadi) {
            $excludedFields = ['id', 'pendaftaran_id', 'created_at', 'updated_at'];
            
            foreach ($this->record->dataPribadi->toArray() as $key => $value) {
                if (!in_array($key, $excludedFields)) {
                    $data[$key] = $value;
                }
            }
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hapus field yang tidak perlu disimpan
        unset($data['created_at'], $data['updated_at']);
        return $data;
    }

protected function afterSave(): void
{
    $data = $this->form->getState();
    
    Log::info('💾 Saving Data Pribadi', [
        'pendaftaran_id' => $this->record->id,
    ]);

    try {
        DB::transaction(function () use ($data) {
            // Bersihkan data - hanya field yang ada di database
            $cleanData = [];
            $allowedFields = [
                'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
                'agama', 'golongan_darah', 'tinggi_badan', 'berat_badan',
                'status_dalam_keluarga', 'hobi', 'riwayat_penyakit',
                'status_alergi', 'keterangan_alergi', 'bahasa_asing_dikuasai',
                'alamat_lengkap', 'desa_kelurahan', 'kecamatan', 'kota_kabupaten',
                'provinsi', 'no_hp', 'pilihan_jurusan', 'pas_foto'
            ];

            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $cleanData[$field] = $data[$field];
                }
            }
            
            $this->record->dataPribadi()->updateOrCreate(
                ['pendaftaran_id' => $this->record->id],
                $cleanData
            );

            if ($this->needsRevision()) {
                $verifikasi = $this->record->latestVerifikasi;
                if ($verifikasi) {
                    $verifikasi->update(['data_pribadi_ok' => true]);

                    $allFixed = $verifikasi->fresh()->data_pribadi_ok &&
                               $verifikasi->fresh()->data_orang_tua_ok &&
                               $verifikasi->fresh()->sekolah_asal_ok &&
                               $verifikasi->fresh()->nilai_raport_ok &&
                               $verifikasi->fresh()->prestasi_organisasi_ok;

                    if ($allFixed) {
                        $this->record->update([
                            'status' => 'submitted',
                            'submitted_at' => now(),
                        ]);
                    }
                }
            }
        });

        // Redirect setelah semua proses selesai
        Notification::make()
            ->title('✅ Data Pribadi Tersimpan!')
            ->success()
            ->body('Melanjutkan ke Data Orang Tua...')
            ->send();
        
        $this->redirect(PendaftaranResource::getUrl('edit-data-orang-tua', ['record' => $this->record]));
            
    } catch (\Exception $e) {
        Log::error('❌ Error: ' . $e->getMessage());
        
        Notification::make()
            ->title('❌ Gagal Menyimpan')
            ->danger()
            ->body($e->getMessage())
            ->send();
            
        throw $e; // ✅ PENTING: Re-throw agar Filament tahu ada error
    }
}

protected function getHeaderActions(): array
{
    return [
        // Tidak ada tombol kembali karena ini halaman pertama
        
        Actions\Action::make('next')
            ->label('Lanjut ke Data Orang Tua →')
            ->icon('heroicon-o-arrow-right')
            ->color('info')
            ->url(fn () => PendaftaranResource::getUrl('edit-data-orang-tua', ['record' => $this->record])),
    ];
}

    // ✅ TAMBAHKAN METHOD INI UNTUK FIX CANCEL BUTTON
protected function getFormActions(): array
{
    // Jika readonly (submitted/approved/rejected/sudah diverifikasi), tidak ada tombol simpan
    if ($this->isFormReadonly() && !$this->needsRevision()) {
        return [
            Actions\Action::make('back_to_list')
                ->label('← Kembali ke Daftar')
                ->color('gray')
                ->url($this->getRedirectUrl())
                ->icon('heroicon-o-arrow-left'),
        ];
    }
    
    return [
        $this->getSaveFormAction()
            ->label('💾 Simpan & Lanjutkan')
            ->icon('heroicon-o-check-circle')
            ->color('primary')
            ->size('lg'),
        
        Actions\Action::make('cancel')
            ->label('Batal')
            ->color('gray')
            ->url($this->getRedirectUrl())
            ->icon('heroicon-o-x-mark'),
    ];
}

}