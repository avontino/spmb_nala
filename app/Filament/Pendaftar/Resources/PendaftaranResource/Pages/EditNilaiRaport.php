<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use App\Filament\Pendaftar\Resources\PendaftaranResource\Pages\Concerns\PreventEditWhenSubmitted;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class EditNilaiRaport extends EditRecord
{
    use PreventEditWhenSubmitted;

    protected static string $resource = PendaftaranResource::class;
    
    protected string $verificationField = 'nilai_raport_ok';
    
    protected static ?string $title = '📊 Edit Nilai Raport';
    protected static ?string $navigationLabel = 'Nilai Raport';
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?int $navigationSort = 4;
    
    public function getBreadcrumb(): string
    {
        return 'Nilai Raport';
    }

    /**
     * Calculate average for a semester
     */
    protected function calculateAverage(Get $get, int $semester): string
    {
        $values = [
            $get("sem{$semester}_nilai_agama"),
            $get("sem{$semester}_nilai_ppkn"),
            $get("sem{$semester}_nilai_bahasa_indonesia"),
            $get("sem{$semester}_nilai_matematika"),
            $get("sem{$semester}_nilai_ipa"),
            $get("sem{$semester}_nilai_ips"),
            $get("sem{$semester}_nilai_bahasa_inggris"),
            $get("sem{$semester}_nilai_seni_budaya"),
            $get("sem{$semester}_nilai_pjok"),
            $get("sem{$semester}_nilai_prakarya"),
        ];

        $validValues = array_filter($values, fn($v) => $v !== null && $v !== '');
        
        if (count($validValues) === 0) {
            return 'Belum ada nilai';
        }

        $average = array_sum($validValues) / count($validValues);
        
        return number_format($average, 2) . ' (dari ' . count($validValues) . ' mata pelajaran)';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

                // KETERANGAN UNTUK MTs
                Section::make()
                    ->schema([
                        Placeholder::make('mts_info')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
                                        ℹ️ Keterangan untuk Siswa MTs (Madrasah Tsanawiyah)
                                    </h3>
                                    <p class="text-sm text-blue-800 dark:text-blue-200">
                                        Untuk kolom <strong>Pendidikan Agama</strong>, silakan isi dengan <strong>rata-rata dari 4 mata pelajaran:</strong>
                                    </p>
                                    <ul class="list-disc list-inside text-sm text-blue-800 dark:text-blue-200 mt-1 ml-2">
                                        <li>Aqidah Akhlak</li>
                                        <li>Al-Quran Hadits</li>
                                        <li>Sejarah Kebudayaan Islam (SKI)</li>
                                        <li>Fiqih</li>
                                    </ul>
                                </div>
                            ')),
                    ]),

                // SECTION NILAI RAPORT - SEMESTER 1-4
                Section::make('📊 Nilai Raport Semester 1-4')
                    ->description('Masukkan nilai untuk setiap mata pelajaran per semester. Rata-rata akan dihitung otomatis.')
                    ->schema([
                        // SEMESTER 1
                        Section::make('📚 Semester 1')
                            ->collapsible()
                            ->schema([
                                TextInput::make('sem1_semester')
                                    ->default(1)
                                    ->hidden()
                                    ->dehydrated(),

                                Grid::make(5)->schema([
                                    TextInput::make('sem1_nilai_agama')
                                        ->label('Pendidikan Agama')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem1_nilai_ppkn')
                                        ->label('Pendidikan Pancasila')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem1_nilai_bahasa_indonesia')
                                        ->label('B. Indonesia')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem1_nilai_matematika')
                                        ->label('Matematika')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem1_nilai_ipa')
                                        ->label('IPA')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem1_nilai_ips')
                                        ->label('IPS')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem1_nilai_bahasa_inggris')
                                        ->label('B. Inggris')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem1_nilai_seni_budaya')
                                        ->label('Seni Budaya dan Prakarya')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem1_nilai_pjok')
                                        ->label('PJOK')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem1_nilai_prakarya')
                                        ->label('Informatika')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),
                                ]),

                                Placeholder::make('sem1_rata_rata')
                                    ->label('Rata-rata Semester 1')
                                    ->content(fn (Get $get) => $this->calculateAverage($get, 1))
                                    ->columnSpanFull(),
                            ]),

                        // SEMESTER 2
                        Section::make('📚 Semester 2')
                            ->collapsible()
                            ->schema([
                                TextInput::make('sem2_semester')
                                    ->default(2)
                                    ->hidden()
                                    ->dehydrated(),

                                Grid::make(5)->schema([
                                    TextInput::make('sem2_nilai_agama')
                                        ->label('Pendidikan Agama')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem2_nilai_ppkn')
                                        ->label('Pendidikan Pancasila')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem2_nilai_bahasa_indonesia')
                                        ->label('B. Indonesia')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem2_nilai_matematika')
                                        ->label('Matematika')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem2_nilai_ipa')
                                        ->label('IPA')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem2_nilai_ips')
                                        ->label('IPS')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem2_nilai_bahasa_inggris')
                                        ->label('B. Inggris')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem2_nilai_seni_budaya')
                                        ->label('Seni Budaya dan Prakarya')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem2_nilai_pjok')
                                        ->label('PJOK')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem2_nilai_prakarya')
                                        ->label('Informatika')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),
                                ]),

                                Placeholder::make('sem2_rata_rata')
                                    ->label('Rata-rata Semester 2')
                                    ->content(fn (Get $get) => $this->calculateAverage($get, 2))
                                    ->columnSpanFull(),
                            ]),

                        // SEMESTER 3
                        Section::make('📚 Semester 3')
                            ->collapsible()
                            ->schema([
                                TextInput::make('sem3_semester')
                                    ->default(3)
                                    ->hidden()
                                    ->dehydrated(),

                                Grid::make(5)->schema([
                                    TextInput::make('sem3_nilai_agama')
                                        ->label('Pendidikan Agama')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem3_nilai_ppkn')
                                        ->label('Pendidikan Pancasila')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem3_nilai_bahasa_indonesia')
                                        ->label('B. Indonesia')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem3_nilai_matematika')
                                        ->label('Matematika')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem3_nilai_ipa')
                                        ->label('IPA')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem3_nilai_ips')
                                        ->label('IPS')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem3_nilai_bahasa_inggris')
                                        ->label('B. Inggris')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem3_nilai_seni_budaya')
                                        ->label('Seni Budaya dan Prakarya')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem3_nilai_pjok')
                                        ->label('PJOK')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem3_nilai_prakarya')
                                        ->label('Informatika')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),
                                ]),

                                Placeholder::make('sem3_rata_rata')
                                    ->label('Rata-rata Semester 3')
                                    ->content(fn (Get $get) => $this->calculateAverage($get, 3))
                                    ->columnSpanFull(),
                            ]),

                        // SEMESTER 4
                        Section::make('📚 Semester 4')
                            ->collapsible()
                            ->schema([
                                TextInput::make('sem4_semester')
                                    ->default(4)
                                    ->hidden()
                                    ->dehydrated(),

                                Grid::make(5)->schema([
                                    TextInput::make('sem4_nilai_agama')
                                        ->label('Pendidikan Agama')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem4_nilai_ppkn')
                                        ->label('Pendidikan Pancasila')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem4_nilai_bahasa_indonesia')
                                        ->label('B. Indonesia')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem4_nilai_matematika')
                                        ->label('Matematika')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem4_nilai_ipa')
                                        ->label('IPA')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem4_nilai_ips')
                                        ->label('IPS')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem4_nilai_bahasa_inggris')
                                        ->label('B. Inggris')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem4_nilai_seni_budaya')
                                        ->label('Seni Budaya dan Prakarya')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem4_nilai_pjok')
                                        ->label('PJOK')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),

                                    TextInput::make('sem4_nilai_prakarya')
                                        ->label('Informatika')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true),
                                ]),

                                Placeholder::make('sem4_rata_rata')
                                    ->label('Rata-rata Semester 4')
                                    ->content(fn (Get $get) => $this->calculateAverage($get, 4))
                                    ->columnSpanFull(),
                            ]),
                         // ✅ BARU: RATA-RATA SEMUA SEMESTER
                    Section::make('📈 Rata-Rata Semua Semester')
                        ->description('Rekap nilai rata-rata dari semester 1 sampai 4')
                        ->schema([
                            Grid::make(4)
                                ->schema([
                                    Placeholder::make('total_sem1')
                                        ->label('Semester 1')
                                        ->content(fn (Get $get) => $this->calculateAverageValue($get, 1))
                                        ->extraAttributes(['class' => 'text-center']),
                                    
                                    Placeholder::make('total_sem2')
                                        ->label('Semester 2')
                                        ->content(fn (Get $get) => $this->calculateAverageValue($get, 2))
                                        ->extraAttributes(['class' => 'text-center']),
                                    
                                    Placeholder::make('total_sem3')
                                        ->label('Semester 3')
                                        ->content(fn (Get $get) => $this->calculateAverageValue($get, 3))
                                        ->extraAttributes(['class' => 'text-center']),
                                    
                                    Placeholder::make('total_sem4')
                                        ->label('Semester 4')
                                        ->content(fn (Get $get) => $this->calculateAverageValue($get, 4))
                                        ->extraAttributes(['class' => 'text-center']),
                                ]),
                            
                            // RATA-RATA KESELURUHAN
                            Placeholder::make('rata_rata_keseluruhan')
                                ->label('🎯 Rata-Rata Keseluruhan (Semester 1-4)')
                                ->content(fn (Get $get) => $this->calculateOverallAverage($get))
                                ->extraAttributes([
                                    'class' => 'bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-700 rounded-lg p-4 text-center font-bold'
                                ])
                                ->columnSpanFull(),
                        ]),
                
                    ]),

                // UPLOAD TRANSKRIP NILAI
                Section::make('📄 Upload Transkrip Nilai')
                    ->description('Upload transkrip nilai raport semester 1-4')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('file_transkrip_nilai')
                            ->label('Transkrip Nilai Raport (Semester 1-4)')
                            ->required()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('transkrip-nilai')
                            ->visibility('public')
                            ->openable()
                            ->downloadable()
                            ->previewable(false)
                            ->helperText('Upload transkrip nilai semester 1-4 dalam 1 file PDF (maksimal 5MB)')
                            ->columnSpanFull(),
                    ]),

                // PREVIEW TRANSKRIP
                Section::make('📊 Preview Transkrip Nilai')
                    ->description('Lihat transkrip nilai yang sudah diupload')
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        ViewField::make('file_transkrip_nilai_preview')
                            ->label('📑 Transkrip Nilai Semester 1-4')
                            ->view('filament.components.pdf-viewer')
                            ->viewData(fn ($record) => [
                                'url' => $record?->dataSekolahAsal?->file_transkrip_nilai 
                                    ? asset('storage/' . $record->dataSekolahAsal->file_transkrip_nilai) 
                                    : null
                            ])
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record?->dataSekolahAsal?->file_transkrip_nilai),
                    ])
                    ->visible(fn ($record) => $record?->dataSekolahAsal?->file_transkrip_nilai),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->nilaiRaport) {
            foreach ($this->record->nilaiRaport as $nilai) {
                $sem = $nilai->semester;
                if ($sem <= 4) {
                    foreach ($nilai->toArray() as $key => $value) {
                        if (!in_array($key, ['id', 'pendaftaran_id', 'semester', 'created_at', 'updated_at'])) {
                            $data["sem{$sem}_{$key}"] = $value;
                        }
                    }
                    $data["sem{$sem}_semester"] = $sem;
                }
            }
        }
        
        if ($this->record->dataSekolahAsal && $this->record->dataSekolahAsal->file_transkrip_nilai) {
            $data['file_transkrip_nilai'] = $this->record->dataSekolahAsal->file_transkrip_nilai;
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [];
    }

protected function afterSave(): void
{
    $data = $this->form->getState();
    
    Log::info('💾 Saving Nilai Raport', [
        'pendaftaran_id' => $this->record->id,
        'is_revision_mode' => $this->needsRevision(),
    ]);

    try {
        DB::transaction(function () use ($data) {
            // 1. Save nilai raport
            $this->record->nilaiRaport()->delete();
            
            for ($sem = 1; $sem <= 4; $sem++) {
                $nilaiData = [
                    'semester' => $sem,
                    'nilai_agama' => $data["sem{$sem}_nilai_agama"] ?? null,
                    'nilai_ppkn' => $data["sem{$sem}_nilai_ppkn"] ?? null,
                    'nilai_bahasa_indonesia' => $data["sem{$sem}_nilai_bahasa_indonesia"] ?? null,
                    'nilai_matematika' => $data["sem{$sem}_nilai_matematika"] ?? null,
                    'nilai_ipa' => $data["sem{$sem}_nilai_ipa"] ?? null,
                    'nilai_ips' => $data["sem{$sem}_nilai_ips"] ?? null,
                    'nilai_bahasa_inggris' => $data["sem{$sem}_nilai_bahasa_inggris"] ?? null,
                    'nilai_seni_budaya' => $data["sem{$sem}_nilai_seni_budaya"] ?? null,
                    'nilai_pjok' => $data["sem{$sem}_nilai_pjok"] ?? null,
                    'nilai_prakarya' => $data["sem{$sem}_nilai_prakarya"] ?? null,
                ];
                
                $this->record->nilaiRaport()->create($nilaiData);
            }

            // 2. Save file transkrip jika ada
            if (isset($data['file_transkrip_nilai'])) {
                $filePath = is_array($data['file_transkrip_nilai']) 
                    ? ($data['file_transkrip_nilai'][0] ?? null)
                    : $data['file_transkrip_nilai'];
                    
                if ($filePath) {
                    $this->record->dataSekolahAsal()->updateOrCreate(
                        ['pendaftaran_id' => $this->record->id],
                        ['file_transkrip_nilai' => $filePath]
                    );
                }
            }

            $this->record->refresh();

            // 3. ✅ HANDLE VERIFIKASI BERDASARKAN MODE
            if ($this->needsRevision()) {
                // === MODE REVISION ===
                // User sedang memperbaiki data yang diminta verifikator
                $verifikasi = $this->record->latestVerifikasi;
                
                if ($verifikasi) {
                    // Paksa set TRUE karena user sudah perbaiki
                    $verifikasi->update([
                        'nilai_raport_ok' => true,
                    ]);

                    Log::info('✅ Revision: Set nilai_raport_ok = true', [
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
                        // Semua sudah diperbaiki, kembalikan ke submitted untuk reverifikasi
                        $this->record->update([
                            'status' => 'submitted',
                            'submitted_at' => now(),
                        ]);
                        
                        Log::info('✅ All sections fixed, status changed to submitted');
                    }
                }
            } else {
                // === MODE DRAFT/NORMAL ===
                // User sedang mengisi data biasa (belum submit atau belum ada revisi)
                $isComplete = $this->record->isNilaiRaportComplete();
                $verifikasi = $this->record->latestVerifikasi;
                
                if ($verifikasi) {
                    // UPDATE existing verifikasi dengan hasil completeness check
                    $verifikasi->update([
                        'nilai_raport_ok' => $isComplete,
                    ]);
                    
                    Log::info('✅ Draft: Updated existing verifikasi', [
                        'verifikasi_id' => $verifikasi->id,
                        'nilai_raport_ok' => $isComplete,
                    ]);
                } else {
                    // CREATE new verifikasi HANYA SEKALI (saat pertama kali save)
                    $verifikasi = $this->record->verifikasi()->create([
                        'verifikator_id' => null,
                        'status' => 'pending',
                        'data_pribadi_ok' => false,
                        'data_orang_tua_ok' => false,
                        'sekolah_asal_ok' => false,
                        'nilai_raport_ok' => $isComplete,
                        'prestasi_organisasi_ok' => false,
                    ]);
                    
                    Log::info('✅ Draft: Created new verifikasi record', [
                        'verifikasi_id' => $verifikasi->id,
                        'nilai_raport_ok' => $isComplete,
                    ]);
                }
            }
        });

        // 4. ✅ REDIRECT BERDASARKAN MODE
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
                    ->title('✅ Nilai Raport Berhasil Diperbaiki!')
                    ->success()
                    ->body('Silakan lanjutkan memperbaiki bagian lainnya jika ada.')
                    ->send();
                
                $this->redirect(PendaftaranResource::getUrl('index'));
            }
        } else {
            // MODE DRAFT/NORMAL
            Notification::make()
                ->title('✅ Nilai Raport Tersimpan!')
                ->success()
                ->body('Melanjutkan ke Prestasi & Organisasi...')
                ->duration(2000)
                ->send();
            
            $this->redirect(PendaftaranResource::getUrl('edit-prestasi', ['record' => $this->record]));
        }
            
    } catch (\Exception $e) {
        Log::error('❌ Error saving nilai raport: ' . $e->getMessage());
        
        Notification::make()
            ->title('❌ Gagal Menyimpan Data')
            ->danger()
            ->body('Terjadi kesalahan: ' . $e->getMessage())
            ->send();
            
        throw $e;
    }
}

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('prev')
                ->label('← Sekolah Asal')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => PendaftaranResource::getUrl('edit-sekolah-asal', ['record' => $this->record])),
            
            Actions\Action::make('next')
                ->label('Prestasi & Organisasi →')
                ->icon('heroicon-o-arrow-right')
                ->color('info')
                ->url(fn () => PendaftaranResource::getUrl('edit-prestasi', ['record' => $this->record])),
        ];
    }

        // ✅ DISABLE REDIRECT DEFAULT FILAMENT
    protected function getRedirectUrl(): ?string
    {
        return null;
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

    protected function calculateAverageValue(Get $get, int $semester): string
{
    $values = [
        $get("sem{$semester}_nilai_agama"),
        $get("sem{$semester}_nilai_ppkn"),
        $get("sem{$semester}_nilai_bahasa_indonesia"),
        $get("sem{$semester}_nilai_matematika"),
        $get("sem{$semester}_nilai_ipa"),
        $get("sem{$semester}_nilai_ips"),
        $get("sem{$semester}_nilai_bahasa_inggris"),
        $get("sem{$semester}_nilai_seni_budaya"),
        $get("sem{$semester}_nilai_pjok"),
        $get("sem{$semester}_nilai_prakarya"),
    ];

    $validValues = array_filter($values, fn($v) => $v !== null && $v !== '');
    
    if (count($validValues) === 0) {
        return '0.00';
    }

    $average = array_sum($validValues) / count($validValues);
    
    return number_format($average, 2);
}
    protected function calculateOverallAverage(Get $get): string
{
    $allAverages = [];
    
    for ($sem = 1; $sem <= 4; $sem++) {
        $values = [
            $get("sem{$sem}_nilai_agama"),
            $get("sem{$sem}_nilai_ppkn"),
            $get("sem{$sem}_nilai_bahasa_indonesia"),
            $get("sem{$sem}_nilai_matematika"),
            $get("sem{$sem}_nilai_ipa"),
            $get("sem{$sem}_nilai_ips"),
            $get("sem{$sem}_nilai_bahasa_inggris"),
            $get("sem{$sem}_nilai_seni_budaya"),
            $get("sem{$sem}_nilai_pjok"),
            $get("sem{$sem}_nilai_prakarya"),
        ];

        $validValues = array_filter($values, fn($v) => $v !== null && $v !== '');
        
        if (count($validValues) > 0) {
            $average = array_sum($validValues) / count($validValues);
            $allAverages[] = $average;
        }
    }
    
    if (count($allAverages) === 0) {
        return '0.00 - Silakan isi nilai untuk semua semester';
    }
    
    $overallAverage = array_sum($allAverages) / count($allAverages);
    
    // Tampilkan informasi jumlah semester yang sudah diisi
    $filledSemesters = count($allAverages);
    $semesterInfo = $filledSemesters === 4 ? '4 semester' : "{$filledSemesters} dari 4 semester";
    
    return number_format($overallAverage, 2) . " (dari {$semesterInfo})";
}


}