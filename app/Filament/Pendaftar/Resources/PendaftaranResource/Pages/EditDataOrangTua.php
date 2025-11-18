<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use App\Filament\Pendaftar\Resources\PendaftaranResource\Pages\Concerns\PreventEditWhenSubmitted;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\ViewField;
use Illuminate\Support\Facades\Storage;

class EditDataOrangTua extends EditRecord
{
    use PreventEditWhenSubmitted;
    
    protected static string $resource = PendaftaranResource::class;
    
    // ✅ SET VERIFICATION FIELD
    protected string $verificationField = 'data_orang_tua_ok';
    
    protected static ?string $title = '👨‍👩‍👧 Edit Data Orang Tua';
    protected static ?string $navigationLabel = 'Data Orang Tua';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;
    
    public function getBreadcrumb(): string
    {
        return 'Data Orang Tua';
    }

    public function form(Form $form): Form
    {
return $form
    ->schema([
        // BANNER REVISION (MERAH) - tetap sama
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

        // BANNER READONLY (HIJAU) - tetap sama
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

        // ✅ SECTION DATA AYAH - DENGAN NIK DAN PEKERJAAN LAINNYA
        Section::make('Data Ayah')
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('nama_ayah')
                            ->label('Nama Ayah')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama lengkap ayah kandung'),

                        // ✅ BARU: NIK AYAH
                        TextInput::make('nik_ayah')
                            ->label('NIK Ayah')
                            ->required()
                            ->maxLength(16)
                            ->minLength(16)
                            ->numeric()
                            ->placeholder('16 digit NIK')
                            ->helperText('Nomor Induk Kependudukan (16 digit)'),

                        Select::make('status_ayah')
                            ->label('Status Ayah')
                            ->required()
                            ->options([
                                'Masih Hidup' => 'Masih Hidup',
                                'Sudah Meninggal' => 'Sudah Meninggal',
                            ])
                            ->default('Masih Hidup'),

                        Select::make('pendidikan_ayah')
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
                            ])
                            ->default('SMA/SMK/MA'),

                        // ✅ UPDATED: PEKERJAAN DENGAN KONDISI LAINNYA
                        Select::make('pekerjaan_ayah')
                            ->label('Pekerjaan Ayah')
                            ->required()
                            ->options([
                                'Buruh' => 'Buruh',
                                'Petani' => 'Petani',
                                'Wiraswasta' => 'Wiraswasta',
                                'PNS' => 'PNS',
                                'Polri/TNI' => 'Polri/TNI',
                                'Perangkat Desa' => 'Perangkat Desa',
                                'Nelayan' => 'Nelayan',
                                'Guru/Dosen' => 'Guru/Dosen',
                                'Dokter/Tenaga Medis' => 'Dokter/Tenaga Medis',
                                'Karyawan Swasta' => 'Karyawan Swasta',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->default('Wiraswasta')
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('pekerjaan_ayah_lainnya', null)),

                        // ✅ BARU: PEKERJAAN AYAH LAINNYA
                        TextInput::make('pekerjaan_ayah_lainnya')
                            ->label('Sebutkan Pekerjaan Ayah')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Tuliskan pekerjaan ayah...')
                            ->visible(fn (Get $get) => $get('pekerjaan_ayah') === 'Lainnya'),

                        Select::make('penghasilan_ayah')
                            ->label('Penghasilan Ayah per Bulan')
                            ->required()
                            ->options([
                                'Kurang dari Rp 1.000.000' => 'Kurang dari Rp 1.000.000',
                                'Rp 1.000.000 - Rp 2.500.000' => 'Rp 1.000.000 - Rp 2.500.000',
                                'Rp 2.500.000 - Rp 5.000.000' => 'Rp 2.500.000 - Rp 5.000.000',
                                'Rp 5.000.000 - Rp 10.000.000' => 'Rp 5.000.000 - Rp 10.000.000',
                                'Lebih dari Rp 10.000.000' => 'Lebih dari Rp 10.000.000',
                            ])
                            ->default('Rp 1.000.000 - Rp 2.500.000'),
                    ]),
            ]),

        // ✅ SECTION DATA IBU - DENGAN NIK DAN PEKERJAAN LAINNYA
        Section::make('Data Ibu')
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('nama_ibu')
                            ->label('Nama Ibu')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama lengkap ibu kandung'),

                        // ✅ BARU: NIK IBU
                        TextInput::make('nik_ibu')
                            ->label('NIK Ibu')
                            ->required()
                            ->maxLength(16)
                            ->minLength(16)
                            ->numeric()
                            ->placeholder('16 digit NIK')
                            ->helperText('Nomor Induk Kependudukan (16 digit)'),

                        Select::make('status_ibu')
                            ->label('Status Ibu')
                            ->required()
                            ->options([
                                'Masih Hidup' => 'Masih Hidup',
                                'Sudah Meninggal' => 'Sudah Meninggal',
                            ])
                            ->default('Masih Hidup'),

                        Select::make('pendidikan_ibu')
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
                            ])
                            ->default('SMA/SMK/MA'),

                        // ✅ UPDATED: PEKERJAAN DENGAN KONDISI LAINNYA
                        Select::make('pekerjaan_ibu')
                            ->label('Pekerjaan Ibu')
                            ->required()
                            ->options([
                                'Ibu Rumah Tangga' => 'Ibu Rumah Tangga',
                                'Buruh' => 'Buruh',
                                'Petani' => 'Petani',
                                'Wiraswasta' => 'Wiraswasta',
                                'PNS' => 'PNS',
                                'Polri/TNI' => 'Polri/TNI',
                                'Perangkat Desa' => 'Perangkat Desa',
                                'Nelayan' => 'Nelayan',
                                'Guru/Dosen' => 'Guru/Dosen',
                                'Dokter/Tenaga Medis' => 'Dokter/Tenaga Medis',
                                'Karyawan Swasta' => 'Karyawan Swasta',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->default('Ibu Rumah Tangga')
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('pekerjaan_ibu_lainnya', null)),

                        // ✅ BARU: PEKERJAAN IBU LAINNYA
                        TextInput::make('pekerjaan_ibu_lainnya')
                            ->label('Sebutkan Pekerjaan Ibu')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Tuliskan pekerjaan ibu...')
                            ->visible(fn (Get $get) => $get('pekerjaan_ibu') === 'Lainnya'),

                        Select::make('penghasilan_ibu')
                            ->label('Penghasilan Ibu per Bulan')
                            ->required()
                            ->options([
                                'Tidak Berpenghasilan' => 'Tidak Berpenghasilan',
                                'Kurang dari Rp 1.000.000' => 'Kurang dari Rp 1.000.000',
                                'Rp 1.000.000 - Rp 2.500.000' => 'Rp 1.000.000 - Rp 2.500.000',
                                'Rp 2.500.000 - Rp 5.000.000' => 'Rp 2.500.000 - Rp 5.000.000',
                                'Rp 5.000.000 - Rp 10.000.000' => 'Rp 5.000.000 - Rp 10.000.000',
                                'Lebih dari Rp 10.000.000' => 'Lebih dari Rp 10.000.000',
                            ])
                            ->default('Tidak Berpenghasilan'),
                    ]),
            ]),

        // ✅ SECTION KEUANGAN KELUARGA - TERPISAH
        Section::make('💰 Keuangan Keluarga')
            ->description('Total pemasukan dan pengeluaran orang tua per bulan')
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('total_pemasukan')
                            ->label('Total Pemasukan Orang Tua per Bulan')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('5000000')
                            ->helperText('Jumlah total penghasilan ayah + ibu per bulan'),

                        TextInput::make('total_pengeluaran')
                            ->label('Total Pengeluaran Orang Tua per Bulan')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('4000000')
                            ->helperText('Estimasi total pengeluaran keluarga per bulan'),
                    ]),
            ]),

        // ✅ SECTION KONTAK - TERPISAH
        Section::make('📱 Kontak Orang Tua')
            ->schema([
                TextInput::make('no_hp_ortu')
                    ->label('No. HP/WhatsApp Orang Tua')
                    ->required()
                    ->tel()
                    ->prefix('+62')
                    ->maxLength(15)
                    ->placeholder('81234567890')
                    ->helperText('Nomor yang bisa dihubungi')
                    ->columnSpanFull(),
            ]),

        // ✅ SECTION DATA WALI - DENGAN STATUS WALI
        Section::make('👤 Data Wali (Opsional)')
            ->description('Isi jika memiliki wali selain orang tua kandung')
            ->collapsible()
            ->collapsed(true)
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('nama_wali')
                            ->label('Nama Wali')
                            ->maxLength(255)
                            ->placeholder('Nama lengkap wali'),

                        // ✅ BARU: STATUS WALI
                        Select::make('status_wali')
                            ->label('Status Wali')
                            ->options([
                                'Paman' => 'Paman',
                                'Bibi' => 'Bibi',
                                'Ayah Tiri' => 'Ayah Tiri',
                                'Ibu Tiri' => 'Ibu Tiri',
                                'Kakak' => 'Kakak',
                                'Kakek' => 'Kakek',
                                'Nenek' => 'Nenek',
                                'Saudara' => 'Saudara',
                                'Lain-lain' => 'Lain-lain',
                            ])
                            ->placeholder('Pilih status wali'),

                        Select::make('pendidikan_wali')
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

                        TextInput::make('pekerjaan_wali')
                            ->label('Pekerjaan Wali')
                            ->maxLength(255)
                            ->placeholder('Pekerjaan wali'),
                    ]),

                FileUpload::make('file_ktp_wali')
                    ->label('KTP Wali')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(2048)
                    ->disk('public')
                    ->directory('ktp-wali')
                    ->visibility('public')
                    ->openable()
                    ->downloadable()
                    ->previewable(false)
                    ->helperText('Upload KTP wali (PDF/JPG/PNG, maks 2MB)')
                    ->columnSpanFull(),

                    // PREVIEW KTP WALI (jika ada)
                    Section::make('📄 Preview KTP Wali')
                        ->description('Lihat KTP wali yang sudah diupload')
                        ->collapsible()
                        ->collapsed(false)
                        ->schema([
                            \Filament\Forms\Components\ViewField::make('file_ktp_wali_preview')
                                ->label('📑 KTP Wali')
                                ->view('filament.components.pdf-viewer')
                                ->viewData(fn ($record) => [
                                    'url' => $record?->dataOrangTua?->file_ktp_wali 
                                        ? asset('storage/' . $record->dataOrangTua->file_ktp_wali) 
                                        : null
                                ])
                                ->columnSpanFull()
                                ->visible(fn ($record) => $record?->dataOrangTua?->file_ktp_wali),
                        ])
                        ->visible(fn ($record) => $record?->dataOrangTua?->file_ktp_wali),
            ]),

        // SECTION DOKUMEN PENDUKUNG - tetap sama
        Section::make('📁 Dokumen Pendukung')
            ->description('Upload dokumen yang diperlukan')
            ->collapsible()
            ->schema([
                FileUpload::make('file_kk_ktp')
                    ->label('Kartu Keluarga & KTP Orang Tua')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(5120)
                    ->disk('public')
                    ->directory('kk-ktp')
                    ->visibility('public')
                    ->openable()
                    ->downloadable()
                    ->previewable(false)
                    ->helperText('Upload dalam 1 file PDF atau gambar (maks 5MB)')
                    ->columnSpanFull(),

                // PREVIEW KK & KTP
                    Section::make('📄 Preview Kartu Keluarga & KTP')
                        ->description('Lihat dokumen KK & KTP yang sudah diupload')
                        ->collapsible()
                        ->collapsed(false)
                        ->schema([
                            \Filament\Forms\Components\ViewField::make('file_kk_ktp_preview')
                                ->label('📑 Kartu Keluarga & KTP')
                                ->view('filament.components.pdf-viewer')
                                ->viewData(fn ($record) => [
                                    'url' => $record?->dataOrangTua?->file_kk_ktp 
                                        ? asset('storage/' . $record->dataOrangTua->file_kk_ktp) 
                                        : null
                                ])
                                ->columnSpanFull()
                                ->visible(fn ($record) => $record?->dataOrangTua?->file_kk_ktp),
                        ])
                        ->visible(fn ($record) => $record?->dataOrangTua?->file_kk_ktp),

                FileUpload::make('file_slip_gaji')
                    ->label('Slip Gaji / Surat Keterangan Penghasilan')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(2048)
                    ->disk('public')
                    ->directory('slip-gaji')
                    ->visibility('public')
                    ->openable()
                    ->downloadable()
                    ->previewable(false)
                    ->helperText('Upload PDF/Gambar slip gaji atau surat keterangan (maks 2MB)')
                    ->columnSpanFull(),

                    // PREVIEW SLIP GAJI
                    Section::make('📄 Preview Slip Gaji')
                        ->description('Lihat slip gaji yang sudah diupload')
                        ->collapsible()
                        ->collapsed(false)
                        ->schema([
                            \Filament\Forms\Components\ViewField::make('file_slip_gaji_preview')
                                ->label('📑 Slip Gaji / Surat Keterangan Penghasilan')
                                ->view('filament.components.pdf-viewer')
                                ->viewData(fn ($record) => [
                                    'url' => $record?->dataOrangTua?->file_slip_gaji 
                                        ? asset('storage/' . $record->dataOrangTua->file_slip_gaji) 
                                        : null
                                ])
                                ->columnSpanFull()
                                ->visible(fn ($record) => $record?->dataOrangTua?->file_slip_gaji),
                        ])
                        ->visible(fn ($record) => $record?->dataOrangTua?->file_slip_gaji),
            ]),
    ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->load('dataOrangTua');
        
        if ($this->record->dataOrangTua) {
            $excludedFields = ['id', 'pendaftaran_id', 'created_at', 'updated_at'];
            
            foreach ($this->record->dataOrangTua->toArray() as $key => $value) {
                if (!in_array($key, $excludedFields)) {
                    $data[$key] = $value;
                }
            }
            
            Log::debug('📂 Data file dari database:', [
                'file_kk_ktp' => $this->record->dataOrangTua->file_kk_ktp ?? 'NULL',
                'file_ktp_wali' => $this->record->dataOrangTua->file_ktp_wali ?? 'NULL', 
                'file_slip_gaji' => $this->record->dataOrangTua->file_slip_gaji ?? 'NULL',
            ]);
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['created_at'], $data['updated_at']);
        Log::debug('Data sebelum disimpan:', $data);
        return $data;
    }

protected function afterSave(): void
{
    $data = $this->form->getState();
    
    Log::info('💾 Saving Data Orang Tua', [
        'pendaftaran_id' => $this->record->id,
        'fields' => count($data),
        'has_file_kk' => isset($data['file_kk_ktp']),
        'is_revision_mode' => $this->needsRevision(),
    ]);

    try {
        DB::transaction(function () use ($data) {
            // 1. Save data orang tua
            $cleanData = $this->cleanDataForSave($data);
            
            $this->record->dataOrangTua()->updateOrCreate(
                ['pendaftaran_id' => $this->record->id],
                $cleanData
            );

            $this->record->refresh();

            // 2. ✅ HANDLE VERIFIKASI BERDASARKAN MODE
            if ($this->needsRevision()) {
                // === MODE REVISION ===
                $verifikasi = $this->record->latestVerifikasi;
                
                if ($verifikasi) {
                    $verifikasi->update(['data_orang_tua_ok' => true]);

                    Log::info('✅ Revision: Set data_orang_tua_ok = true', [
                        'verifikasi_id' => $verifikasi->id,
                    ]);

                    $verifikasi->refresh();
                    $allFixed = $verifikasi->data_pribadi_ok &&
                               $verifikasi->data_orang_tua_ok &&
                               $verifikasi->sekolah_asal_ok &&
                               $verifikasi->nilai_raport_ok &&
                               $verifikasi->prestasi_organisasi_ok;

                    if ($allFixed) {
                        $this->record->update([
                            'status' => 'submitted',
                            'submitted_at' => now(),
                        ]);
                        
                        Log::info('✅ All sections fixed, status changed to submitted');
                    }
                }
            } else {
                // === MODE DRAFT/NORMAL ===
                $isComplete = $this->record->isDataOrangTuaComplete();
                $verifikasi = $this->record->latestVerifikasi;
                
                if ($verifikasi) {
                    $verifikasi->update(['data_orang_tua_ok' => $isComplete]);
                    
                    Log::info('✅ Draft: Updated existing verifikasi', [
                        'verifikasi_id' => $verifikasi->id,
                        'data_orang_tua_ok' => $isComplete,
                    ]);
                } else {
                    $verifikasi = $this->record->verifikasi()->create([
                        'verifikator_id' => null,
                        'status' => 'pending',
                        'data_pribadi_ok' => false,
                        'data_orang_tua_ok' => $isComplete,
                        'sekolah_asal_ok' => false,
                        'nilai_raport_ok' => false,
                        'prestasi_organisasi_ok' => false,
                    ]);
                    
                    Log::info('✅ Draft: Created new verifikasi record', [
                        'verifikasi_id' => $verifikasi->id,
                        'data_orang_tua_ok' => $isComplete,
                    ]);
                }
            }
        });

        // 3. ✅ REDIRECT BERDASARKAN MODE
        if ($this->needsRevision()) {
            // MODE REVISION
            $verifikasi = $this->record->fresh()->latestVerifikasi;
            $allFixed = $verifikasi &&
                       $verifikasi->data_pribadi_ok &&
                       $verifikasi->data_orang_tua_ok &&
                       $verifikasi->sekolah_asal_ok &&
                       $verifikasi->nilai_raport_ok &&
                       $verifikasi->prestasi_organisasi_ok;

            if ($allFixed) {
                Notification::make()
                    ->title('✅ Semua Perbaikan Selesai!')
                    ->success()
                    ->body('Semua bagian sudah diperbaiki. Pendaftaran telah dikirim untuk verifikasi ulang.')
                    ->persistent()
                    ->send();
                
                $this->redirect(PendaftaranResource::getUrl('index'));
            } else {
                Notification::make()
                    ->title('✅ Data Orang Tua Berhasil Diperbaiki!')
                    ->success()
                    ->body('Silakan lanjutkan memperbaiki bagian lainnya jika ada.')
                    ->send();
                
                $this->redirect(PendaftaranResource::getUrl('index'));
            }
        } else {
            // MODE DRAFT/NORMAL
            Notification::make()
                ->title('✅ Data Orang Tua Tersimpan!')
                ->success()
                ->body('Data berhasil disimpan. Melanjutkan ke Sekolah Asal...')
                ->duration(2000)
                ->send();
            
            $this->redirect(PendaftaranResource::getUrl('edit-sekolah-asal', ['record' => $this->record]));
        }
            
    } catch (\Exception $e) {
        Log::error('❌ Error saving data orang tua: ' . $e->getMessage(), [
            'data' => $data,
            'error_trace' => $e->getTraceAsString()
        ]);
        
        Notification::make()
            ->title('❌ Gagal Menyimpan Data')
            ->danger()
            ->body('Terjadi kesalahan: ' . $e->getMessage())
            ->send();
            
        throw $e;
    }
}

    protected function cleanDataForSave(array $data): array
    {
        $cleanData = [];
        
        $fieldTypes = [
            'nama_ayah' => 'string',
            'nik_ayah' => 'string',
            'status_ayah' => 'string',
            'pendidikan_ayah' => 'string', 
            'pekerjaan_ayah' => 'string',
            'pekerjaan_ayah_lainnya' => 'string',
            'penghasilan_ayah' => 'string',
            'nama_ibu' => 'string',
            'nik_ibu' => 'string',
            'status_ibu' => 'string',
            'pendidikan_ibu' => 'string',
            'pekerjaan_ibu' => 'string',
            'pekerjaan_ibu_lainnya' => 'string',
            'penghasilan_ibu' => 'string',
            'no_hp_ortu' => 'string',
            'nama_wali' => 'string',
            'status_wali' => 'string',
            'pendidikan_wali' => 'string',
            'pekerjaan_wali' => 'string',
            'total_pemasukan' => 'integer',
            'total_pengeluaran' => 'integer',
            'file_kk_ktp' => 'string',
            'file_ktp_wali' => 'string', 
            'file_slip_gaji' => 'string',
        ];
        
        foreach ($fieldTypes as $field => $type) {
            if (array_key_exists($field, $data)) {
                switch ($type) {
                    case 'integer':
                        $cleanData[$field] = (int) ($data[$field] ?? 0);
                        break;
                    case 'string':
                        if (is_array($data[$field])) {
                            $cleanData[$field] = $data[$field][0] ?? '';
                        } else {
                            $cleanData[$field] = (string) ($data[$field] ?? '');
                        }
                        break;
                    default:
                        $cleanData[$field] = $data[$field];
                }
            }
        }
        
        unset($cleanData['created_at'], $cleanData['updated_at']);
        
        Log::debug('📁 Data bersih yang akan disimpan:', $cleanData);
        
        return $cleanData;
    }

protected function getHeaderActions(): array
{
    return [
        Actions\Action::make('prev')
            ->label('← Data Pribadi')
            ->icon('heroicon-o-arrow-left')
            ->color('gray')
            ->url(fn () => PendaftaranResource::getUrl('edit-data-pribadi', ['record' => $this->record])),
        
        Actions\Action::make('next')
            ->label('Sekolah Asal →')
            ->icon('heroicon-o-arrow-right')
            ->color('info')
            ->url(fn () => PendaftaranResource::getUrl('edit-sekolah-asal', ['record' => $this->record])),
    ];
}

protected function getFormActions(): array
{
    // Jika readonly, tidak ada tombol simpan
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


    protected function beforeSave(): void
    {
        $data = $this->form->getState();
        
        if (isset($data['total_pemasukan']) && isset($data['total_pengeluaran'])) {
            if ($data['total_pengeluaran'] > $data['total_pemasukan']) {
                Notification::make()
                    ->title('⚠️ Peringatan')
                    ->warning()
                    ->body('Total pengeluaran lebih besar dari pemasukan.')
                    ->send();
            }
        }
    }
}