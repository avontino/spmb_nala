<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use App\Filament\Pendaftar\Resources\PendaftaranResource\Pages\Concerns\PreventEditWhenSubmitted;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class EditPrestasiOrganisasi extends EditRecord
{
    use PreventEditWhenSubmitted;

    protected static string $resource = PendaftaranResource::class;
    
    protected string $verificationField = 'prestasi_organisasi_ok';
    
    protected static ?string $title = '🏆 Edit Prestasi & Organisasi';
    protected static ?string $navigationLabel = 'Prestasi & Organisasi';
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?int $navigationSort = 5;
    
    public function getBreadcrumb(): string
    {
        return 'Prestasi & Organisasi';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // BANNER INFO - Wajib Klik Simpan
                Section::make()
                    ->schema([
                        Placeholder::make('info_save')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-500 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">
                                            ℹ️ Informasi Penting
                                        </h3>
                                        <p class="text-sm text-blue-700 dark:text-blue-300">
                                            <strong>Wajib klik tombol "Simpan" di bawah</strong> meskipun tidak memiliki prestasi atau organisasi. 
                                            Ini diperlukan untuk menandai bahwa Anda sudah mengisi bagian ini.
                                        </p>
                                    </div>
                                </div>
                            ')),
                    ])
                    ->visible(fn () => !$this->record->prestasi_organisasi_completed && $this->record->status === 'draft'),

                // BANNER REVISION (MERAH)
                Section::make()
                    ->schema([
                        Placeholder::make('revision_alert')
                            ->label('')
                            ->content(function () {
                                $verifikasi = $this->record->latestVerifikasi;
                                return new HtmlString('
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

                // BANNER READONLY (HIJAU)
                Section::make()
                    ->schema([
                        Placeholder::make('readonly_alert')
                            ->label('')
                            ->content(new HtmlString('
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

                // OPSI STATUS KEPEMILIKAN (Page 6)
                Section::make('📋 Status Prestasi & Organisasi')
                    ->description('Tentukan apakah Anda memiliki prestasi atau pengalaman organisasi')
                    ->schema([
                        Radio::make('status_kepemilikan')
                            ->label('Apakah Anda memiliki prestasi dan/atau pengalaman organisasi?')
                            ->options([
                                'tidak_punya' => 'Tidak Punya Prestasi dan Organisasi',
                                'punya' => 'Punya Prestasi / Organisasi',
                            ])
                            ->default('tidak_punya')
                            ->live()
                            ->required()
                            ->helperText('Jika memilih "Tidak Punya", menu isian prestasi & organisasi tidak akan dimunculkan dan Anda cukup menekan tombol "Simpan & Lanjutkan" di bawah.'),
                    ]),

                // SECTION PRESTASI
                Section::make('🏆 Prestasi yang Diraih')
                    ->description('Pilih maksimal 3 prestasi terbaik. Tingkat minimal Kota/Kabupaten.')
                    ->visible(fn (Get $get) => $get('status_kepemilikan') === 'punya')
                    ->schema([
                        Placeholder::make('prestasi_info')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                        <strong>⚠️ Catatan:</strong> Pilih <strong>3 prestasi terbaik</strong> Anda. 
                                        Prestasi yang dicantumkan minimal tingkat <strong>Kota/Kabupaten</strong>.
                                    </p>
                                </div>
                            ')),

                        Repeater::make('prestasi')
                            ->relationship('prestasi')
                            ->schema([
                                TextInput::make('urutan')
                                    ->label('Prestasi ke-')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(3)
                                    ->default(1),
                                    
                                TextInput::make('nama_prestasi')
                                    ->label('Nama Prestasi')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Juara 1 Olimpiade Matematika'),
                                    
                                Select::make('tingkat_prestasi')
                                    ->label('Tingkat')
                                    ->required()
                                    ->options([
                                        'Kota/Kabupaten' => 'Kota/Kabupaten',
                                        'Provinsi' => 'Provinsi',
                                        'Nasional' => 'Nasional',
                                        'Internasional' => 'Internasional',
                                    ])
                                    ->native(false)
                                    ->helperText('Minimal tingkat Kota/Kabupaten'),
                                    
                                FileUpload::make('file_bukti_prestasi')
                                    ->label('Bukti Prestasi (Sertifikat/Piagam)')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                    ->maxSize(2048)
                                    ->disk('public')
                                    ->directory('bukti-prestasi')
                                    ->visibility('public')
                                    ->openable()
                                    ->downloadable()
                                    ->previewable(false)
                                    ->helperText('Upload sertifikat/piagam prestasi (PDF/JPG/PNG, maksimal 2MB)'),
                            ])
                            ->columns(2)
                            ->minItems(0)
                            ->maxItems(3)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->addActionLabel('+ Tambah Prestasi')
                            ->itemLabel(fn (array $state): ?string => $state['nama_prestasi'] ?? 'Prestasi Baru'),
                    ]),

                // SECTION ORGANISASI (Page 6 & Page 8)
                Section::make('🏫 Pengalaman Organisasi')
                    ->description('Organisasi internal sekolah asal saja (OSIS, Pramuka, PMR, dll). Bukan organisasi di luar sekolah.')
                    ->visible(fn (Get $get) => $get('status_kepemilikan') === 'punya')
                    ->schema([
                        Placeholder::make('organisasi_info')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-300 dark:border-blue-700 rounded-lg">
                                    <p class="text-sm text-blue-800 dark:text-blue-200">
                                        <strong>ℹ️ Catatan:</strong> Isi organisasi <strong>internal sekolah asal</strong> saja (OSIS, Pramuka, PMR, Rohis, dll). 
                                        <strong>Bukan</strong> organisasi di luar sekolah. Jabatan yang dicantumkan harus sebagai <strong>pengurus</strong> (bukan anggota biasa).
                                    </p>
                                </div>
                            ')),

                        Repeater::make('organisasi')
                            ->relationship('organisasi')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('nama_organisasi')
                                            ->label('Nama Organisasi Internal Sekolah')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Contoh: OSIS, Pramuka, PMR, Rohis, Paskibra')
                                            ->helperText('Organisasi di dalam sekolah asal'),
                                            
                                        Select::make('jabatan')
                                            ->label('Jabatan/Posisi (Pengurus)')
                                            ->required()
                                            ->options([
                                                'Ketua' => 'Ketua',
                                                'Wakil Ketua' => 'Wakil Ketua',
                                                'Sekretaris' => 'Sekretaris',
                                                'Bendahara' => 'Bendahara',
                                                'Koordinator Bidang' => 'Koordinator Bidang',
                                            ])
                                            ->native(false)
                                            ->placeholder('Pilih jabatan')
                                            ->helperText('Harus sebagai pengurus, bukan anggota biasa'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('periode_jabatan')
                                            ->label('Periode Jabatan')
                                            ->maxLength(100)
                                            ->placeholder('Contoh: 2023 - 2024')
                                            ->helperText('Masa periode kepengurusan'),

                                        FileUpload::make('file_bukti_organisasi')
                                            ->label('Bukti Organisasi (SK Pengurus/Sertifikat)')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->maxSize(2048)
                                            ->disk('public')
                                            ->directory('bukti-organisasi')
                                            ->visibility('public')
                                            ->openable()
                                            ->downloadable()
                                            ->previewable(false)
                                            ->helperText('Upload SK pengurus atau sertifikat keaktifan (PDF/JPG/PNG, maksimal 2MB)'),
                                    ]),
                            ])
                            ->columns(1)
                            ->minItems(0)
                            ->maxItems(5)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->addActionLabel('+ Tambah Organisasi')
                            ->itemLabel(fn (array $state): ?string => ($state['nama_organisasi'] ?? 'Organisasi Baru') . (!empty($state['periode_jabatan']) ? ' (' . $state['periode_jabatan'] . ')' : '')),
                    ]),

                // SECTION UPLOAD TRANSKRIP PRESTASI DAN ORGANISASI (Page 7 & Page 9)
                Section::make('📄 Upload Transkrip Prestasi dan Organisasi')
                    ->description('Upload transkrip prestasi dan organisasi yang sudah ditandatangani')
                    ->collapsible()
                    ->visible(fn (Get $get) => $get('status_kepemilikan') === 'punya')
                    ->schema([
                        FileUpload::make('file_transkrip_prestasi')
                            ->label('Transkrip Prestasi dan Organisasi (dengan TTD Kepala Sekolah)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('transkrip-prestasi')
                            ->visibility('public')
                            ->openable()
                            ->downloadable()
                            ->previewable(false)
                            ->helperText('Upload transkrip prestasi dan organisasi yang sudah ditandatangani kepala sekolah (PDF, maksimal 2MB)')
                            ->columnSpanFull(),
                    ]),

                // PREVIEW TRANSKRIP PRESTASI DAN ORGANISASI
                Section::make('📊 Preview Transkrip Prestasi dan Organisasi')
                    ->description('Lihat transkrip prestasi dan organisasi yang sudah diupload')
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(fn (Get $get, $record) => $get('status_kepemilikan') === 'punya' && $record?->dokumenPendukung?->file_transkrip_prestasi)
                    ->schema([
                        ViewField::make('file_transkrip_prestasi_preview')
                            ->label('📑 Transkrip Prestasi dan Organisasi')
                            ->view('filament.components.pdf-viewer')
                            ->viewData(fn ($record) => [
                                'url' => $record?->dokumenPendukung?->file_transkrip_prestasi 
                                    ? asset('storage/' . $record->dokumenPendukung->file_transkrip_prestasi) 
                                    : null
                            ])
                            ->columnSpanFull(),
                    ]),

                // PREVIEW ORGANISASI
                Section::make('📊 Preview Bukti Organisasi')
                    ->description('Lihat Bukti Organisasi yang sudah diupload')
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(fn (Get $get, $record) => $get('status_kepemilikan') === 'punya' && $record && $record->organisasi()->whereNotNull('file_bukti_organisasi')->exists())
                    ->schema(function ($record) {
                        $schemas = [];
                        if ($record) {
                            $orgs = $record->organisasi()->whereNotNull('file_bukti_organisasi')->get();
                            foreach ($orgs as $index => $org) {
                                $num = $index + 1;
                                $schemas[] = ViewField::make("file_bukti_organisasi_{$index}_preview")
                                    ->label("📑 Bukti Organisasi {$num}: " . ($org->nama_organisasi ?? 'Organisasi'))
                                    ->view('filament.components.pdf-viewer')
                                    ->viewData([
                                        'url' => asset('storage/' . $org->file_bukti_organisasi)
                                    ])
                                    ->columnSpanFull();
                            }
                        }
                        return $schemas;
                    }),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->load(['organisasi', 'dokumenPendukung', 'prestasi']);
        
        $hasPrestasi = $this->record->prestasi->isNotEmpty();
        $hasOrganisasi = $this->record->organisasi->isNotEmpty();
        $hasTranskrip = !empty($this->record->dokumenPendukung?->file_transkrip_prestasi);

        $data['status_kepemilikan'] = ($hasPrestasi || $hasOrganisasi || $hasTranskrip) ? 'punya' : 'tidak_punya';
        
        if ($this->record->dokumenPendukung) {
            $data['file_transkrip_prestasi'] = $this->record->dokumenPendukung->file_transkrip_prestasi;
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['created_at'], $data['updated_at']);
        return [];
    }

protected function afterSave(): void
{
    $data = $this->form->getState();
    $statusKepemilikan = $data['status_kepemilikan'] ?? 'tidak_punya';
    
    Log::info('💾 Saving Prestasi & Organisasi', [
        'pendaftaran_id' => $this->record->id,
        'status_kepemilikan' => $statusKepemilikan,
        'is_revision_mode' => $this->needsRevision(),
    ]);

    try {
        DB::transaction(function () use ($data, $statusKepemilikan) {
            if ($statusKepemilikan === 'tidak_punya') {
                $this->record->prestasi()->delete();
                $this->record->organisasi()->delete();
                if ($this->record->dokumenPendukung) {
                    $this->record->dokumenPendukung()->update(['file_transkrip_prestasi' => null]);
                }
            } else {
                // Save Dokumen Pendukung
                $fileTranskrip = $data['file_transkrip_prestasi'] ?? null;
                if (is_array($fileTranskrip)) {
                    $fileTranskrip = $fileTranskrip[0] ?? null;
                }
                
                if ($fileTranskrip) {
                    $this->record->dokumenPendukung()->updateOrCreate(
                        ['pendaftaran_id' => $this->record->id],
                        ['file_transkrip_prestasi' => $fileTranskrip]
                    );
                }
            }

            // TANDAI PRESTASI & ORGANISASI SUDAH DISIMPAN
            $this->record->update(['prestasi_organisasi_completed' => true]);
            $this->record->refresh();

            // 3. ✅ HANDLE VERIFIKASI BERDASARKAN MODE
            if ($this->needsRevision()) {
                // === MODE REVISION ===
                $verifikasi = $this->record->latestVerifikasi;
                
                if ($verifikasi) {
                    $verifikasi->update([
                        'prestasi_organisasi_ok' => true,
                    ]);

                    Log::info('✅ Revision: Set prestasi_organisasi_ok = true', [
                        'verifikasi_id' => $verifikasi->id,
                    ]);

                    // Cek apakah semua bagian sudah diperbaiki
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
                $isComplete = $this->record->isPrestasiOrganisasiComplete(); // Akan return true karena flag sudah di-set
                $verifikasi = $this->record->latestVerifikasi;
                
                if ($verifikasi) {
                    $verifikasi->update([
                        'prestasi_organisasi_ok' => $isComplete,
                    ]);
                    
                    Log::info('✅ Draft: Updated existing verifikasi', [
                        'verifikasi_id' => $verifikasi->id,
                        'prestasi_organisasi_ok' => $isComplete,
                    ]);
                } else {
                    $verifikasi = $this->record->verifikasi()->create([
                        'verifikator_id' => null,
                        'status' => 'pending',
                        'data_pribadi_ok' => $this->record->isDataPribadiComplete(),
                        'data_orang_tua_ok' => $this->record->isDataOrangTuaComplete(),
                        'sekolah_asal_ok' => $this->record->isDataSekolahAsalComplete(),
                        'nilai_raport_ok' => $this->record->isNilaiRaportComplete(),
                        'prestasi_organisasi_ok' => $isComplete,
                    ]);
                    
                    Log::info('✅ Draft: Created new verifikasi record', [
                        'verifikasi_id' => $verifikasi->id,
                        'prestasi_organisasi_ok' => $isComplete,
                    ]);
                }
            }
        });

        // 4. ✅ REDIRECT BERDASARKAN MODE
        if ($this->needsRevision()) {
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
                    ->title('✅ Prestasi & Organisasi Berhasil Diperbaiki!')
                    ->success()
                    ->body('Silakan lanjutkan memperbaiki bagian lainnya jika ada.')
                    ->send();
                
                $this->redirect(PendaftaranResource::getUrl('index'));
            }
        } else {
            // MODE DRAFT/NORMAL
            $freshRecord = $this->record->fresh();
            
            // ✅ GUNAKAN canSubmit() YANG SUDAH BENAR
            if ($freshRecord->canSubmit()) {
                Notification::make()
                    ->title('🎉 Semua Data Lengkap!')
                    ->success()
                    ->body('Data berhasil disimpan. Anda sekarang dapat menyelesaikan pendaftaran.')
                    ->duration(3000)
                    ->send();
                
                $this->redirect(PendaftaranResource::getUrl('submit', ['record' => $this->record]));
            } else {
                // ✅ TAMPILKAN PESAN YANG LEBIH INFORMATIF
                $completionStatus = $freshRecord->getCompletionStatus();
                $incompleteSections = array_filter($completionStatus, fn($status) => !$status);
                
                $message = 'Data berhasil disimpan. ';
                if (count($incompleteSections) > 0) {
                    $message .= 'Silakan lengkapi data: ' . implode(', ', array_keys($incompleteSections));
                }
                
                Notification::make()
                    ->title('✅ Prestasi & Organisasi Tersimpan!')
                    ->success()
                    ->body($message)
                    ->duration(4000)
                    ->send();
                
                $this->redirect(PendaftaranResource::getUrl('index'));
            }
        }
            
    } catch (\Exception $e) {
        Log::error('❌ Error saving prestasi & organisasi: ' . $e->getMessage());
        
        Notification::make()
            ->title('❌ Gagal Menyimpan Data')
            ->danger()
            ->body('Terjadi kesalahan: ' . $e->getMessage())
            ->send();
            
        // Catat error tanpa melempar 500 server error
    }
}

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('prev')
                ->label('← Nilai Raport')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => PendaftaranResource::getUrl('edit-nilai-raport', ['record' => $this->record])),
            
            Actions\Action::make('next_submit')
                ->label('🚀 Submit Pendaftaran')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->url(fn () => PendaftaranResource::getUrl('submit', ['record' => $this->record]))
                ->visible(fn () => $this->record->fresh()->canSubmit()),
        ];
    }

    protected function getFormActions(): array
    {
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
                ->label('💾 Simpan & Selesai')
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