<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use App\Filament\Pendaftar\Resources\PendaftaranResource\Pages\Concerns\PreventEditWhenSubmitted;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditDataSekolahAsal extends EditRecord
{
    use PreventEditWhenSubmitted;
    
    protected static string $resource = PendaftaranResource::class;
    
    // ✅ SET VERIFICATION FIELD
    protected string $verificationField = 'sekolah_asal_ok';
    
    protected static ?string $title = '🎓 Edit Data Sekolah Asal';
    protected static ?string $navigationLabel = 'Sekolah Asal';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 3;
    
    public function getBreadcrumb(): string
    {
        return 'Sekolah Asal';
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

        Section::make('Informasi Sekolah Asal')
            ->description('Isi data sekolah asal dengan lengkap dan benar')
            ->schema([
                Grid::make(2)
                    ->schema([
                        Select::make('jenjang_sekolah')
                            ->label('Jenjang Sekolah Asal')
                            ->required()
                            ->options([
                                'SMP' => 'SMP',
                                'MTs' => 'MTs',
                            ])
                            ->default('SMP')
                            ->native(false),

                        TextInput::make('nama_sekolah')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: SMPN 1 Jakarta'),

                        Select::make('status_sekolah')
                            ->label('Status Sekolah')
                            ->required()
                            ->options([
                                'Negeri' => 'Negeri',
                                'Swasta' => 'Swasta',
                            ])
                            ->default('Negeri')
                            ->native(false),

                        // ✅ BARU: NPSN
                        TextInput::make('npsn')
                            ->label('NPSN Sekolah')
                            ->required()
                            ->numeric()
                            ->placeholder('NPSN SMP')
                            ->helperText('Nomor Pokok Sekolah Nasional'),
                    ]),

                Textarea::make('alamat_sekolah')
                    ->label('Alamat Lengkap Sekolah')
                    ->required()
                    ->rows(3)
                    ->placeholder('Jalan, Kecamatan')
                    ->helperText('Alamat lengkap sekolah tanpa kota dan provinsi')
                    ->columnSpanFull(),

                // ✅ BARU: KOTA DAN PROVINSI TERPISAH
                Grid::make(2)
                    ->schema([
                        TextInput::make('kota_sekolah')
                            ->label('Kota/Kabupaten Sekolah')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Jakarta Selatan, Bandung, Surabaya'),

                        Select::make('provinsi_sekolah')
                            ->label('Provinsi Sekolah')
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
                            ->native(false),
                    ]),
            ]),

        // ❌ HAPUS SECTION UPLOAD TRANSKRIP NILAI
        // Dipindahkan ke EditNilaiRaport.php
    ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load relation untuk menghindari N+1 query
        $this->record->load('dataSekolahAsal');
        
        if ($this->record->dataSekolahAsal) {
            $excludedFields = ['id', 'pendaftaran_id', 'created_at', 'updated_at'];
            
            foreach ($this->record->dataSekolahAsal->toArray() as $key => $value) {
                if (!in_array($key, $excludedFields)) {
                    $data[$key] = $value;
                }
            }
            
            // Debug log
            Log::debug('📂 Data transkrip dari database:', [
                'file_transkrip_nilai' => $this->record->dataSekolahAsal->file_transkrip_nilai ?? 'NULL',
            ]);
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hapus field yang tidak perlu disimpan
        unset($data['created_at'], $data['updated_at']);
        
        // Handle array dari file upload
        if (isset($data['file_transkrip_nilai']) && is_array($data['file_transkrip_nilai'])) {
            $data['file_transkrip_nilai'] = $data['file_transkrip_nilai'][0] ?? '';
        }
        
        Log::debug('📁 Data yang akan disimpan:', $data);
        
        return [];
    }

protected function afterSave(): void
{
    $data = $this->form->getState();
    
    Log::info('💾 Saving Data Sekolah Asal', [
        'pendaftaran_id' => $this->record->id,
        'fields' => count($data),
        'has_file' => isset($data['file_transkrip_nilai'])
    ]);

    try {
        DB::transaction(function () use ($data) {
            $cleanData = $this->cleanDataForSave($data);
            
            $this->record->dataSekolahAsal()->updateOrCreate(
                ['pendaftaran_id' => $this->record->id],
                $cleanData
            );

            // Reset verification status saat user edit data yang perlu revisi
            if ($this->needsRevision()) {
                $verifikasi = $this->record->latestVerifikasi;
                if ($verifikasi) {
                    $verifikasi->update([
                        'sekolah_asal_ok' => true,
                    ]);

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

        // ✅ NOTIFIKASI DAN REDIRECT BERDASARKAN KONDISI
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
                    ->title('✅ Data Sekolah Asal Berhasil Diperbaiki!')
                    ->success()
                    ->body('Silakan lanjutkan memperbaiki bagian lainnya jika ada.')
                    ->send();
                
                $this->redirect(PendaftaranResource::getUrl('index'));
            }
        } else {
            // Mode normal (draft) - redirect ke halaman berikutnya
            Notification::make()
                ->title('✅ Data Sekolah Asal Tersimpan!')
                ->success()
                ->body('Data berhasil disimpan. Melanjutkan ke Nilai Raport...')
                ->duration(2000)
                ->send();
            
            $this->redirect(PendaftaranResource::getUrl('edit-nilai-raport', ['record' => $this->record]));
        }
            
    } catch (\Exception $e) {
        Log::error('❌ Error saving data sekolah asal: ' . $e->getMessage(), [
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

    /**
     * Clean data before saving to database
     */
protected function cleanDataForSave(array $data): array
{
    $cleanData = [];
    
    $fieldTypes = [
        'jenjang_sekolah' => 'string',
        'nama_sekolah' => 'string',
        'status_sekolah' => 'string',
        'npsn' => 'string',                    // ✅ BARU
        'alamat_sekolah' => 'string',
        'kota_sekolah' => 'string',            // ✅ BARU
        'provinsi_sekolah' => 'string',        // ✅ BARU
        // 'file_transkrip_nilai' => 'string',  // ❌ HAPUS - pindah ke EditNilaiRaport
    ];
    
    foreach ($fieldTypes as $field => $type) {
        if (array_key_exists($field, $data)) {
            if ($type === 'string') {
                if (is_array($data[$field])) {
                    $cleanData[$field] = $data[$field][0] ?? '';
                } else {
                    $cleanData[$field] = (string) ($data[$field] ?? '');
                }
            } else {
                $cleanData[$field] = $data[$field];
            }
        }
    }
    
    unset($cleanData['created_at'], $cleanData['updated_at']);
    
    return $cleanData;
}

protected function getHeaderActions(): array
{
    return [
        Actions\Action::make('prev')
            ->label('← Data Orang Tua')
            ->icon('heroicon-o-arrow-left')
            ->color('gray')
            ->url(fn () => PendaftaranResource::getUrl('edit-data-orang-tua', ['record' => $this->record])),
        
        Actions\Action::make('next')
            ->label('Nilai Raport →')
            ->icon('heroicon-o-arrow-right')
            ->color('info')
            ->url(fn () => PendaftaranResource::getUrl('edit-nilai-raport', ['record' => $this->record])),
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

    // ✅ DISABLE REDIRECT DEFAULT FILAMENT
    protected function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * Validate before save
     */
    protected function beforeSave(): void
    {
        $data = $this->form->getState();
        
        // Custom validation jika diperlukan
        if (empty($data['nama_sekolah'])) {
            Notification::make()
                ->title('⚠️ Peringatan')
                ->warning()
                ->body('Nama sekolah harus diisi.')
                ->send();
        }
    }
}