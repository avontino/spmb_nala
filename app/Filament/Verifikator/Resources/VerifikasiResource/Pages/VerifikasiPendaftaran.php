<?php

namespace App\Filament\Verifikator\Resources\VerifikasiResource\Pages;

use App\Filament\Verifikator\Resources\VerifikasiResource;
use App\Models\Pendaftaran;
use App\Models\Verifikasi;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Notifications\NeedReviseNotification;
use App\Notifications\ApprovedNotification;
use App\Notifications\RejectedNotification;

class VerifikasiPendaftaran extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = VerifikasiResource::class;

    protected static string $view = 'filament.verifikator.resources.verifikasi-resource.pages.verifikasi-pendaftaran';
    
    protected static ?string $title = 'Verifikasi Pendaftaran';

    public ?array $data = [];

    public bool $allDataComplete = false;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        
        $this->record->load([
            'user',
            'dataPribadi',
            'dataOrangTua',
            'dataSekolahAsal',
            'nilaiRaport' => function ($query) {
                $query->orderBy('semester');
            },
            'prestasi' => function ($query) {
                $query->orderBy('urutan');
            },
            'organisasi',
            'dokumenPendukung',
            'latestVerifikasi'
        ]);

        $latestVerifikasi = $this->record->latestVerifikasi;
        
        $this->form->fill([
            'data_pribadi_ok' => $latestVerifikasi?->data_pribadi_ok ?? false,
            'data_orang_tua_ok' => $latestVerifikasi?->data_orang_tua_ok ?? false,
            'sekolah_asal_ok' => $latestVerifikasi?->sekolah_asal_ok ?? false,
            'nilai_raport_ok' => $latestVerifikasi?->nilai_raport_ok ?? false,
            'prestasi_organisasi_ok' => $latestVerifikasi?->prestasi_organisasi_ok ?? false,
        ]);

        $this->updateButtonState();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pendaftar')
                    ->description('Data identitas dan status pendaftaran')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Placeholder::make('nomor_pendaftaran')
                                    ->label('No. Pendaftaran')
                                    ->content(fn () => $this->record->nomor_pendaftaran ?? '-'),
                                    
                                Placeholder::make('nama')
                                    ->label('Nama Lengkap')
                                    ->content(fn () => $this->record->dataPribadi?->nama_lengkap ?? '-'),
                                    
                                Placeholder::make('tanggal_submit')
                                    ->label('Tanggal Submit')
                                    ->content(fn () => $this->record->submitted_at?->format('d M Y, H:i') ?? '-'),
                                    
                                Placeholder::make('status')
                                    ->label('Status')
                                    ->content(fn () => match($this->record->status) {
                                        'draft' => 'Draft',
                                        'submitted' => 'Menunggu Verifikasi',
                                        'needs_revision' => 'Perlu Perbaikan',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => $this->record->status,
                                    }),
                            ]),
                        
                        Grid::make(4)
                            ->schema([
                                Placeholder::make('nik')
                                    ->label('NIK')
                                    ->content(fn () => $this->record->user?->nik ?? '-'),
                                    
                                Placeholder::make('nokk')
                                    ->label('Nomor KK')
                                    ->content(fn () => $this->record->user?->nokk ?? '-'),
                                    
                                Placeholder::make('nisn')
                                    ->label('NISN')
                                    ->content(fn () => $this->record->user?->nisn ?? '-'),
                                    
                                Placeholder::make('email')
                                    ->label('Email')
                                    ->content(fn () => $this->record->user?->email ?? '-'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Data Pribadi')
                    ->description('Informasi lengkap data pribadi pendaftar')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Placeholder::make('dp_nama')
                                    ->label('Nama Lengkap')
                                    ->content(fn () => $this->record->dataPribadi?->nama_lengkap ?? '-'),
                                    
                                Placeholder::make('dp_jk')
                                    ->label('Jenis Kelamin')
                                    ->content(fn () => $this->record->dataPribadi?->jenis_kelamin ?? '-'),
                                    
                                Placeholder::make('dp_tempat_lahir')
                                    ->label('Tempat Lahir')
                                    ->content(fn () => $this->record->dataPribadi?->tempat_lahir ?? '-'),
                                    
                                Placeholder::make('dp_tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->content(fn () => $this->record->dataPribadi?->tanggal_lahir?->format('d M Y') ?? '-'),
                            ]),
                        
                        Grid::make(4)
                            ->schema([
                                Placeholder::make('dp_agama')
                                    ->label('Agama')
                                    ->content(fn () => $this->record->dataPribadi?->agama ?? '-'),
                                    
                                Placeholder::make('dp_goldar')
                                    ->label('Golongan Darah')
                                    ->content(fn () => $this->record->dataPribadi?->golongan_darah ?? '-'),
                                    
                                Placeholder::make('dp_tinggi')
                                    ->label('Tinggi Badan')
                                    ->content(fn () => $this->record->dataPribadi?->tinggi_badan ? $this->record->dataPribadi->tinggi_badan . ' cm' : '-'),
                                    
                                Placeholder::make('dp_berat')
                                    ->label('Berat Badan')
                                    ->content(fn () => $this->record->dataPribadi?->berat_badan ? $this->record->dataPribadi->berat_badan . ' kg' : '-'),
                            ]),
                        
                        Grid::make(4)
                            ->schema([
                                Placeholder::make('dp_no_hp')
                                    ->label('No. HP')
                                    ->content(fn () => $this->record->dataPribadi?->no_hp ?? '-'),
                                    
                                Placeholder::make('dp_status_keluarga')
                                    ->label('Status dalam Keluarga')
                                    ->content(fn () => $this->record->dataPribadi?->status_dalam_keluarga ?? '-'),
                                    
                                Placeholder::make('dp_hobi')
                                    ->label('Hobi')
                                    ->content(fn () => $this->record->dataPribadi?->hobi ?? '-'),
                                    
                                Placeholder::make('dp_status_alergi')
                                    ->label('Status Alergi')
                                    ->content(fn () => $this->record->dataPribadi?->status_alergi ?? '-'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('dp_keterangan_alergi')
                                    ->label('Keterangan Alergi')
                                    ->content(fn () => $this->record->dataPribadi?->keterangan_alergi ?? '-')
                                    ->visible(fn () => $this->record->dataPribadi?->status_alergi === 'Memiliki Alergi'),
                                    
                                Placeholder::make('dp_bahasa_asing')
                                    ->label('Bahasa Asing yang Dikuasai')
                                    ->content(fn () => $this->record->dataPribadi?->bahasa_asing_dikuasai ?? '-'),
                            ]),
                        
                        Placeholder::make('dp_riwayat_penyakit')
                            ->label('Riwayat Penyakit/Kondisi Kesehatan')
                            ->content(fn () => $this->record->dataPribadi?->riwayat_penyakit ?? '-')
                            ->columnSpanFull(),
                        
                        Placeholder::make('dp_alamat')
                            ->label('Alamat Lengkap')
                            ->content(fn () => $this->record->dataPribadi?->alamat_lengkap ?? '-')
                            ->columnSpanFull(),
                        
                        Grid::make(4)
                            ->schema([
                                Placeholder::make('dp_desa')
                                    ->label('Desa/Kelurahan')
                                    ->content(fn () => $this->record->dataPribadi?->desa_kelurahan ?? '-'),
                                    
                                Placeholder::make('dp_kecamatan')
                                    ->label('Kecamatan')
                                    ->content(fn () => $this->record->dataPribadi?->kecamatan ?? '-'),
                                    
                                Placeholder::make('dp_kota')
                                    ->label('Kota/Kabupaten')
                                    ->content(fn () => $this->record->dataPribadi?->kota_kabupaten ?? '-'),
                                    
                                Placeholder::make('dp_provinsi')
                                    ->label('Provinsi')
                                    ->content(fn () => $this->record->dataPribadi?->provinsi ?? '-'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('dp_rencana_studi')
                                    ->label('Rencana Studi Lanjut Setelah SMA')
                                    ->content(fn () => $this->record->dataPribadi?->pilihan_jurusan ?? '-'),
                                    
                                ViewField::make('pas_foto_viewer')
                                    ->label('Pas Foto')
                                    ->view('filament.components.file-viewer', [
                                        'file' => $this->record->dataPribadi?->pas_foto,
                                        'label' => 'Pas Foto',
                                        'type' => 'image'
                                    ]),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make('Data Orang Tua/Wali')
                    ->description('Informasi lengkap data orang tua dan wali')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Section::make('Data Ayah')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Placeholder::make('do_nama_ayah')
                                            ->label('Nama Ayah')
                                            ->content(fn () => $this->record->dataOrangTua?->nama_ayah ?? '-'),
                                            
                                        Placeholder::make('do_nik_ayah')
                                            ->label('NIK Ayah')
                                            ->content(fn () => $this->record->dataOrangTua?->nik_ayah ?? '-'),
                                            
                                        Placeholder::make('do_status_ayah')
                                            ->label('Status')
                                            ->content(fn () => $this->record->dataOrangTua?->status_ayah ?? '-'),
                                            
                                        Placeholder::make('do_pendidikan_ayah')
                                            ->label('Pendidikan')
                                            ->content(fn () => $this->record->dataOrangTua?->pendidikan_ayah ?? '-'),
                                    ]),
                                
                                Grid::make(2)
                                    ->schema([
                                        Placeholder::make('do_pekerjaan_ayah')
                                            ->label('Pekerjaan')
                                            ->content(function () {
                                                $pekerjaan = $this->record->dataOrangTua?->pekerjaan_ayah ?? '-';
                                                $lainnya = $this->record->dataOrangTua?->pekerjaan_ayah_lainnya;
                                                
                                                if ($pekerjaan === 'Lainnya' && $lainnya) {
                                                    return $lainnya . ' (Lainnya)';
                                                }
                                                return $pekerjaan;
                                            }),
                                            
                                        Placeholder::make('do_penghasilan_ayah')
                                            ->label('Penghasilan')
                                            ->content(fn () => $this->record->dataOrangTua?->penghasilan_ayah ?? '-'),
                                    ]),
                            ])
                            ->collapsible()
                            ->collapsed(false),

                        Section::make('Data Ibu')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Placeholder::make('do_nama_ibu')
                                            ->label('Nama Ibu')
                                            ->content(fn () => $this->record->dataOrangTua?->nama_ibu ?? '-'),
                                            
                                        Placeholder::make('do_nik_ibu')
                                            ->label('NIK Ibu')
                                            ->content(fn () => $this->record->dataOrangTua?->nik_ibu ?? '-'),
                                            
                                        Placeholder::make('do_status_ibu')
                                            ->label('Status')
                                            ->content(fn () => $this->record->dataOrangTua?->status_ibu ?? '-'),
                                            
                                        Placeholder::make('do_pendidikan_ibu')
                                            ->label('Pendidikan')
                                            ->content(fn () => $this->record->dataOrangTua?->pendidikan_ibu ?? '-'),
                                    ]),
                                
                                Grid::make(2)
                                    ->schema([
                                        Placeholder::make('do_pekerjaan_ibu')
                                            ->label('Pekerjaan')
                                            ->content(function () {
                                                $pekerjaan = $this->record->dataOrangTua?->pekerjaan_ibu ?? '-';
                                                $lainnya = $this->record->dataOrangTua?->pekerjaan_ibu_lainnya;
                                                
                                                if ($pekerjaan === 'Lainnya' && $lainnya) {
                                                    return $lainnya . ' (Lainnya)';
                                                }
                                                return $pekerjaan;
                                            }),
                                            
                                        Placeholder::make('do_penghasilan_ibu')
                                            ->label('Penghasilan')
                                            ->content(fn () => $this->record->dataOrangTua?->penghasilan_ibu ?? '-'),
                                    ]),
                            ])
                            ->collapsible()
                            ->collapsed(false),

                        Section::make('Keuangan Keluarga')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Placeholder::make('do_total_pemasukan')
                                            ->label('Total Pemasukan/Bulan')
                                            ->content(fn () => $this->record->dataOrangTua?->total_pemasukan 
                                                ? 'Rp ' . number_format((float) $this->record->dataOrangTua->total_pemasukan, 0, ',', '.') 
                                                : '-'),
                                            
                                        Placeholder::make('do_total_pengeluaran')
                                            ->label('Total Pengeluaran/Bulan')
                                            ->content(fn () => $this->record->dataOrangTua?->total_pengeluaran 
                                                ? 'Rp ' . number_format((float) $this->record->dataOrangTua->total_pengeluaran, 0, ',', '.') 
                                                : '-'),
                                            
                                        Placeholder::make('do_no_hp')
                                            ->label('No. HP Orang Tua')
                                            ->content(fn () => $this->record->dataOrangTua?->no_hp_ortu ?? '-'),
                                    ]),
                            ])
                            ->collapsible()
                            ->collapsed(false),

                        Section::make('Data Wali (Opsional)')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Placeholder::make('do_nama_wali')
                                            ->label('Nama Wali')
                                            ->content(fn () => $this->record->dataOrangTua?->nama_wali ?? '-'),
                                            
                                        Placeholder::make('do_status_wali')
                                            ->label('Status')
                                            ->content(fn () => $this->record->dataOrangTua?->status_wali ?? '-'),
                                            
                                        Placeholder::make('do_pendidikan_wali')
                                            ->label('Pendidikan')
                                            ->content(fn () => $this->record->dataOrangTua?->pendidikan_wali ?? '-'),
                                            
                                        Placeholder::make('do_pekerjaan_wali')
                                            ->label('Pekerjaan')
                                            ->content(fn () => $this->record->dataOrangTua?->pekerjaan_wali ?? '-'),
                                    ]),
                            ])
                            ->visible(fn () => !empty($this->record->dataOrangTua?->nama_wali))
                            ->collapsible()
                            ->collapsed(false),
                        
                        Grid::make(3)
                            ->schema([
                                ViewField::make('file_kk_ktp_viewer')
                                    ->label('File KK & KTP')
                                    ->view('filament.components.file-viewer', [
                                        'file' => $this->record->dataOrangTua?->file_kk_ktp,
                                        'label' => 'KK & KTP',
                                        'type' => 'pdf'
                                    ]),
                                    
                                ViewField::make('file_ktp_wali_viewer')
                                    ->label('File KTP Wali')
                                    ->view('filament.components.file-viewer', [
                                        'file' => $this->record->dataOrangTua?->file_ktp_wali,
                                        'label' => 'KTP Wali',
                                        'type' => 'pdf'
                                    ])
                                    ->visible(fn () => !empty($this->record->dataOrangTua?->file_ktp_wali)),
                                    
                                ViewField::make('file_slip_gaji_viewer')
                                    ->label('File Slip Gaji')
                                    ->view('filament.components.file-viewer', [
                                        'file' => $this->record->dataOrangTua?->file_slip_gaji,
                                        'label' => 'Slip Gaji',
                                        'type' => 'pdf'
                                    ])
                                    ->visible(fn () => !empty($this->record->dataOrangTua?->file_slip_gaji)),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make('Data Sekolah Asal')
                    ->description('Informasi lengkap sekolah asal')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Placeholder::make('sa_nama')
                                    ->label('Nama Sekolah')
                                    ->content(fn () => $this->record->dataSekolahAsal?->nama_sekolah ?? '-'),
                                    
                                Placeholder::make('sa_npsn')
                                    ->label('NPSN')
                                    ->content(fn () => $this->record->dataSekolahAsal?->npsn ?? '-'),
                                    
                                Placeholder::make('sa_jenjang')
                                    ->label('Jenjang')
                                    ->content(fn () => $this->record->dataSekolahAsal?->jenjang_sekolah ?? '-'),
                                    
                                Placeholder::make('sa_status')
                                    ->label('Status Sekolah')
                                    ->content(fn () => $this->record->dataSekolahAsal?->status_sekolah ?? '-'),
                            ]),
                        
                        Placeholder::make('sa_alamat')
                            ->label('Alamat Sekolah')
                            ->content(fn () => $this->record->dataSekolahAsal?->alamat_sekolah ?? '-')
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('sa_kota')
                                    ->label('Kota/Kabupaten')
                                    ->content(fn () => $this->record->dataSekolahAsal?->kota_sekolah ?? '-'),
                                    
                                Placeholder::make('sa_provinsi')
                                    ->label('Provinsi')
                                    ->content(fn () => $this->record->dataSekolahAsal?->provinsi_sekolah ?? '-'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make('Nilai Raport')
                    ->description('Informasi nilai raport per semester')
                    ->icon('heroicon-o-chart-bar')
                    ->schema($this->getNilaiRaportSections())
                    ->collapsible()
                    ->collapsed(true),

                Section::make('Prestasi & Organisasi')
                    ->description('Informasi prestasi dan pengalaman organisasi')
                    ->icon('heroicon-o-trophy')
                    ->schema([
                        Section::make('Daftar Prestasi')
                            ->schema($this->getPrestasiFields())
                            ->collapsible()
                            ->collapsed(false),
                        
                        Section::make('Pengalaman Organisasi')
                            ->schema($this->getOrganisasiFields())
                            ->collapsible()
                            ->collapsed(false),
                        
                        ViewField::make('file_transkrip_prestasi_viewer')
                            ->label('File Transkrip Prestasi')
                            ->view('filament.components.file-viewer', [
                                'file' => $this->record->dokumenPendukung?->file_transkrip_prestasi,
                                'label' => 'Transkrip Prestasi',
                                'type' => 'pdf'
                            ])
                            ->visible(fn () => !empty($this->record->dokumenPendukung?->file_transkrip_prestasi)),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make('Catatan Revisi Sebelumnya')
                    ->description('Catatan dari verifikasi sebelumnya')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Placeholder::make('previous_revision_info')
                            ->label('')
                            ->content(function () {
                                $latestVerifikasi = $this->record->latestVerifikasi;
                                
                                if (!$latestVerifikasi || !$latestVerifikasi->catatan_revisi) {
                                    return new \Illuminate\Support\HtmlString('
                                        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                Belum ada catatan revisi sebelumnya
                                            </span>
                                        </div>
                                    ');
                                }
                                
                                $verifikatorName = $latestVerifikasi->verifikator?->name ?? 'Unknown';
                                $verifiedDate = $latestVerifikasi->verified_at?->format('d M Y, H:i') ?? '-';
                                $status = $latestVerifikasi->status;
                                
                                $statusText = match($status) {
                                    'needs_revision' => 'Perlu Perbaikan',
                                    'rejected' => 'Ditolak',
                                    'approved' => 'Disetujui',
                                    default => $status,
                                };
                                
                                return new \Illuminate\Support\HtmlString('
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-3 gap-4 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                            <div>
                                                <div class="text-xs text-gray-500 mb-1">Verifikator</div>
                                                <div class="font-medium">' . $verifikatorName . '</div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 mb-1">Tanggal</div>
                                                <div class="font-medium">' . $verifiedDate . '</div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 mb-1">Status</div>
                                                <div class="font-medium">' . $statusText . '</div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-4 bg-warning-50 dark:bg-warning-900/20 rounded-lg border-l-4 border-warning-500">
                                            <div class="text-sm font-semibold mb-2">Catatan untuk Pendaftar:</div>
                                            <p class="text-sm whitespace-pre-wrap">' . nl2br(e($latestVerifikasi->catatan_revisi)) . '</p>
                                        </div>
                                        
                                        <div class="grid grid-cols-5 gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg text-center text-sm">
                                            <div>
                                                <div class="text-xs text-gray-500 mb-1">Data Pribadi</div>
                                                <div>' . ($latestVerifikasi->data_pribadi_ok ? '✓' : '✗') . '</div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 mb-1">Orang Tua</div>
                                                <div>' . ($latestVerifikasi->data_orang_tua_ok ? '✓' : '✗') . '</div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 mb-1">Sekolah</div>
                                                <div>' . ($latestVerifikasi->sekolah_asal_ok ? '✓' : '✗') . '</div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 mb-1">Nilai</div>
                                                <div>' . ($latestVerifikasi->nilai_raport_ok ? '✓' : '✗') . '</div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 mb-1">Prestasi</div>
                                                <div>' . ($latestVerifikasi->prestasi_organisasi_ok ? '✓' : '✗') . '</div>
                                            </div>
                                        </div>
                                    </div>
                                ');
                            }),
                    ])
                    ->collapsible()
                    ->collapsed(fn () => !$this->record->latestVerifikasi || !$this->record->latestVerifikasi->catatan_revisi)
                    ->visible(fn () => $this->record->status === 'submitted' || $this->record->status === 'needs_revision'),

                Section::make('Verifikasi Kelengkapan Data')
                    ->description('Periksa dan checklist kelengkapan data')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        \Filament\Forms\Components\Actions::make([
                            FormAction::make('auto_checklist_all')
                                ->label('Checklist Semua')
                                ->color('success')
                                ->icon('heroicon-o-check-badge')
                                ->action(function (Set $set) {
                                    $set('data_pribadi_ok', true);
                                    $set('data_orang_tua_ok', true);
                                    $set('sekolah_asal_ok', true);
                                    $set('nilai_raport_ok', true);
                                    $set('prestasi_organisasi_ok', true);
                                    
                                    $this->updateButtonState();
                                    
                                    Notification::make()
                                        ->title('Semua Data Ter-checklist')
                                        ->success()
                                        ->send();
                                })
                                ->requiresConfirmation()
                                ->modalHeading('Checklist Semua Data?')
                                ->modalDescription('Pastikan Anda sudah memeriksa semua data dengan teliti.')
                                ->modalSubmitActionLabel('Ya, Checklist Semua'),
                                
                            FormAction::make('reset_checklist')
                                ->label('Reset')
                                ->color('danger')
                                ->icon('heroicon-o-x-circle')
                                ->action(function (Set $set) {
                                    $set('data_pribadi_ok', false);
                                    $set('data_orang_tua_ok', false);
                                    $set('sekolah_asal_ok', false);
                                    $set('nilai_raport_ok', false);
                                    $set('prestasi_organisasi_ok', false);
                                    
                                    $this->updateButtonState();
                                    
                                    Notification::make()
                                        ->title('Checklist Di-reset')
                                        ->warning()
                                        ->send();
                                })
                                ->requiresConfirmation(),
                        ]),
                        
                        Grid::make(5)
                            ->schema([
                                Toggle::make('data_pribadi_ok')
                                    ->label('Data Pribadi')
                                    ->helperText($this->getDataPribadiStatus())
                                    ->inline(false)
                                    ->live(onBlur: false)
                                    ->afterStateUpdated(fn () => $this->updateButtonState()),
                                    
                                Toggle::make('data_orang_tua_ok')
                                    ->label('Data Orang Tua')
                                    ->helperText($this->getDataOrangTuaStatus())
                                    ->inline(false)
                                    ->live(onBlur: false)
                                    ->afterStateUpdated(fn () => $this->updateButtonState()),
                                    
                                Toggle::make('sekolah_asal_ok')
                                    ->label('Sekolah Asal')
                                    ->helperText($this->getSekolahAsalStatus())
                                    ->inline(false)
                                    ->live(onBlur: false)
                                    ->afterStateUpdated(fn () => $this->updateButtonState()),
                                    
                                Toggle::make('nilai_raport_ok')
                                    ->label('Nilai Raport')
                                    ->helperText($this->getNilaiRaportStatus())
                                    ->inline(false)
                                    ->live(onBlur: false)
                                    ->afterStateUpdated(fn () => $this->updateButtonState()),
                                    
                                Toggle::make('prestasi_organisasi_ok')
                                    ->label('Prestasi/Organisasi')
                                    ->helperText($this->getPrestasiOrganisasiStatus())
                                    ->inline(false)
                                    ->live(onBlur: false)
                                    ->afterStateUpdated(fn () => $this->updateButtonState()),
                            ]),
                    ]),

                Section::make('Tindakan Verifikasi')
                    ->description('Pilih tindakan setelah verifikasi')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                \Filament\Forms\Components\Actions::make([
                                    FormAction::make('approve_action')
                                        ->label('Setujui')
                                        ->color('success')
                                        ->icon('heroicon-o-check-circle')
                                        ->size('lg')
                                        ->requiresConfirmation()
                                        ->modalHeading('Setujui Pendaftaran?')
                                        ->modalDescription('Status pendaftaran akan berubah menjadi "Disetujui".')
                                        ->modalSubmitActionLabel('Ya, Setujui')
                                        ->disabled(fn () => !$this->allDataComplete)
                                        ->action(fn () => $this->approve()),
                                ]),
                                
                                \Filament\Forms\Components\Actions::make([
                                    FormAction::make('request_revision_action')
                                        ->label('Minta Perbaikan')
                                        ->color('warning')
                                        ->icon('heroicon-o-exclamation-triangle')
                                        ->size('lg')
                                        ->form([
                                            Textarea::make('catatan_revisi')
                                                ->label('Catatan Perbaikan')
                                                ->placeholder('Jelaskan bagian yang perlu diperbaiki...')
                                                ->required()
                                                ->rows(6),
                                        ])
                                        ->action(fn (array $data) => $this->requestRevision($data)),
                                ]),
                                
                                \Filament\Forms\Components\Actions::make([
                                    FormAction::make('reject_action')
                                        ->label('Tolak')
                                        ->color('danger')
                                        ->icon('heroicon-o-x-circle')
                                        ->size('lg')
                                        ->form([
                                            Textarea::make('catatan_penolakan')
                                                ->label('Alasan Penolakan')
                                                ->placeholder('Jelaskan alasan penolakan...')
                                                ->required()
                                                ->rows(6),
                                        ])
                                        ->requiresConfirmation()
                                        ->modalHeading('Tolak Pendaftaran?')
                                        ->modalDescription('Tindakan ini bersifat permanen.')
                                        ->modalSubmitActionLabel('Ya, Tolak')
                                        ->action(fn (array $data) => $this->reject($data)),
                                ]),
                                
                                \Filament\Forms\Components\Actions::make([
                                    FormAction::make('back_action')
                                        ->label('Kembali')
                                        ->icon('heroicon-o-arrow-left')
                                        ->color('gray')
                                        ->size('lg')
                                        ->url(fn () => VerifikasiResource::getUrl('index')),
                                ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getNilaiRaportSections(): array
    {
        $nilaiRaport = $this->record->nilaiRaport;
        
        if ($nilaiRaport->isEmpty()) {
            return [
                Placeholder::make('no_data')
                    ->label('')
                    ->content('Belum ada data nilai raport'),
            ];
        }
        
        $sections = [];
        $totalRataRata = 0;
        $semesterCount = 0;
        
        foreach ($nilaiRaport as $nilai) {
            if ($nilai->semester <= 4) {
                $rataSemester = (float) $nilai->rata_rata;
                $totalRataRata += $rataSemester;
                $semesterCount++;
                
                $sections[] = Section::make('Semester ' . $nilai->semester . ' - Rata-rata: ' . number_format($rataSemester, 2))
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                Placeholder::make('agama_' . $nilai->semester)
                                    ->label('Pend. Agama')
                                    ->content($nilai->nilai_agama ?? '-'),
                                    
                                Placeholder::make('ppkn_' . $nilai->semester)
                                    ->label('PPKN')
                                    ->content($nilai->nilai_ppkn ?? '-'),
                                    
                                Placeholder::make('bindo_' . $nilai->semester)
                                    ->label('B. Indonesia')
                                    ->content($nilai->nilai_bahasa_indonesia ?? '-'),
                                    
                                Placeholder::make('mtk_' . $nilai->semester)
                                    ->label('Matematika')
                                    ->content($nilai->nilai_matematika ?? '-'),
                                    
                                Placeholder::make('ipa_' . $nilai->semester)
                                    ->label('IPA')
                                    ->content($nilai->nilai_ipa ?? '-'),
                            ]),
                        
                        Grid::make(5)
                            ->schema([
                                Placeholder::make('ips_' . $nilai->semester)
                                    ->label('IPS')
                                    ->content($nilai->nilai_ips ?? '-'),
                                    
                                Placeholder::make('bing_' . $nilai->semester)
                                    ->label('B. Inggris')
                                    ->content($nilai->nilai_bahasa_inggris ?? '-'),
                                    
                                Placeholder::make('senbud_' . $nilai->semester)
                                    ->label('Seni Budaya')
                                    ->content($nilai->nilai_seni_budaya ?? '-'),
                                    
                                Placeholder::make('pjok_' . $nilai->semester)
                                    ->label('PJOK')
                                    ->content($nilai->nilai_pjok ?? '-'),
                                    
                                Placeholder::make('prakarya_' . $nilai->semester)
                                    ->label('Prakarya')
                                    ->content($nilai->nilai_prakarya ?? '-'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true);
            }
        }
        
        if ($semesterCount > 0) {
            $overallAverage = $totalRataRata / $semesterCount;
            array_unshift($sections, 
                Placeholder::make('overall_average')
                    ->label('Rata-Rata Keseluruhan (Semester 1-4)')
                    ->content(number_format($overallAverage, 2))
            );
        }
        
        $sections[] = ViewField::make('file_transkrip_nilai_viewer')
            ->label('File Transkrip Nilai')
            ->view('filament.components.file-viewer', [
                'file' => $this->record->dataSekolahAsal?->file_transkrip_nilai,
                'label' => 'Transkrip Nilai',
                'type' => 'pdf'
            ]);
        
        return $sections;
    }

    protected function getPrestasiFields(): array
    {
        $prestasi = $this->record->prestasi;
        
        if ($prestasi->isEmpty()) {
            return [
                Placeholder::make('no_prestasi')
                    ->label('')
                    ->content('Tidak ada data prestasi'),
            ];
        }
        
        $fields = [];
        
        foreach ($prestasi as $index => $item) {
            $fields[] = Grid::make(3)
                ->schema([
                    Placeholder::make('prestasi_nama_' . $index)
                        ->label('Nama Prestasi')
                        ->content($item->nama_prestasi),
                        
                    Placeholder::make('prestasi_tingkat_' . $index)
                        ->label('Tingkat')
                        ->content($item->tingkat_prestasi),
                        
                    ViewField::make('prestasi_bukti_' . $index)
                        ->label('Bukti')
                        ->view('filament.components.file-viewer', [
                            'file' => $item->file_bukti_prestasi,
                            'label' => 'Bukti Prestasi',
                            'type' => 'pdf'
                        ])
                        ->visible(fn () => !empty($item->file_bukti_prestasi)),
                ]);
        }
        
        return $fields;
    }

    protected function getOrganisasiFields(): array
    {
        $organisasi = $this->record->organisasi;
        
        if (!$organisasi || empty($organisasi->nama_organisasi)) {
            return [
                Placeholder::make('no_organisasi')
                    ->label('')
                    ->content('Tidak ada data organisasi'),
            ];
        }
        
        return [
            Grid::make(3)
                ->schema([
                    Placeholder::make('organisasi_nama')
                        ->label('Nama Organisasi')
                        ->content($organisasi->nama_organisasi),
                        
                    Placeholder::make('organisasi_jabatan')
                        ->label('Jabatan')
                        ->content($organisasi->jabatan),
                        
                    ViewField::make('organisasi_bukti')
                        ->label('Bukti')
                        ->view('filament.components.file-viewer', [
                            'file' => $organisasi->file_bukti_organisasi,
                            'label' => 'Bukti Organisasi',
                            'type' => 'pdf'
                        ])
                        ->visible(fn () => !empty($organisasi->file_bukti_organisasi)),
                ]),
        ];
    }

    public function updateButtonState(): void
    {
        $this->allDataComplete = $this->isAllDataComplete();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function approve(): void
    {
        try {
            DB::transaction(function () {
                Verifikasi::updateOrCreate(
                    ['pendaftaran_id' => $this->record->id],
                    [
                        'verifikator_id' => auth()->id(),
                        'status' => 'approved',
                        'data_pribadi_ok' => true,
                        'data_orang_tua_ok' => true,
                        'sekolah_asal_ok' => true,
                        'nilai_raport_ok' => true,
                        'prestasi_organisasi_ok' => true,
                        'catatan' => null,
                        'catatan_revisi' => null,
                        'verified_at' => now(),
                    ]
                );

                $this->record->update([
                    'status' => 'approved',
                    'verified_at' => now(),
                ]);
            });

            Notification::make()
                ->title('Pendaftaran Disetujui')
                ->success()
                ->body('Pendaftar akan mendapatkan notifikasi.')
                ->send();
            
            $emailData = [
                'nomor_pendaftaran' => $this->record->nomor_pendaftaran,
                'catatan_revisi' => null,
            ];
            $this->record->user->notify((new ApprovedNotification($emailData))->delay(now()->addSeconds(10)));

            $this->redirect(VerifikasiResource::getUrl('index'));
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menyetujui')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function requestRevision(array $data): void
    {
        try {
            DB::transaction(function () use ($data) {
                Verifikasi::updateOrCreate(
                    ['pendaftaran_id' => $this->record->id],
                    [
                        'verifikator_id' => auth()->id(),
                        'status' => 'needs_revision',
                        'data_pribadi_ok' => $this->data['data_pribadi_ok'] ?? false,
                        'data_orang_tua_ok' => $this->data['data_orang_tua_ok'] ?? false,
                        'sekolah_asal_ok' => $this->data['sekolah_asal_ok'] ?? false,
                        'nilai_raport_ok' => $this->data['nilai_raport_ok'] ?? false,
                        'prestasi_organisasi_ok' => $this->data['prestasi_organisasi_ok'] ?? false,
                        'catatan' => null,
                        'catatan_revisi' => $data['catatan_revisi'],
                        'verified_at' => now(),
                    ]
                );

                $this->record->update([
                    'status' => 'needs_revision',
                ]);
            });

            Notification::make()
                ->title('Permintaan Perbaikan Terkirim')
                ->warning()
                ->body('Pendaftar akan menerima notifikasi.')
                ->send();

            // send notification to the registrant, use queue job
            $emailData = [
                'nomor_pendaftaran' => $this->record->nomor_pendaftaran,
                'catatan_revisi' => $data['catatan_revisi'],
            ];
            $this->record->user->notify((new NeedReviseNotification($emailData))->delay(now()->addSeconds(10)));

            $this->redirect(VerifikasiResource::getUrl('index'));
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Mengirim')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function reject(array $data): void
    {
        try {
            DB::transaction(function () use ($data) {
                Verifikasi::updateOrCreate(
                    ['pendaftaran_id' => $this->record->id],
                    [
                        'verifikator_id' => auth()->id(),
                        'status' => 'rejected',
                        'data_pribadi_ok' => $this->data['data_pribadi_ok'] ?? false,
                        'data_orang_tua_ok' => $this->data['data_orang_tua_ok'] ?? false,
                        'sekolah_asal_ok' => $this->data['sekolah_asal_ok'] ?? false,
                        'nilai_raport_ok' => $this->data['nilai_raport_ok'] ?? false,
                        'prestasi_organisasi_ok' => $this->data['prestasi_organisasi_ok'] ?? false,
                        'catatan' => null,
                        'catatan_revisi' => $data['catatan_penolakan'],
                        'verified_at' => now(),
                    ]
                );

                $this->record->update([
                    'status' => 'rejected',
                ]);
            });

            Notification::make()
                ->title('Pendaftaran Ditolak')
                ->danger()
                ->body('Pendaftar akan menerima notifikasi.')
                ->send();
            
            $emailData = [
                'nomor_pendaftaran' => $this->record->nomor_pendaftaran,
                'catatan_revisi' => $data['catatan_penolakan'],
            ];
            $this->record->user->notify((new RejectedNotification($emailData))->delay(now()->addSeconds(10)));

            $this->redirect(VerifikasiResource::getUrl('index'));
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menolak')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getDataPribadiStatus(): string
    {
        $data = $this->record->dataPribadi;
        
        if (!$data) {
            return 'Belum diisi';
        }
        
        $required = ['nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat_lengkap', 'no_hp'];
        $filled = 0;
        
        foreach ($required as $field) {
            if (filled($data->$field)) {
                $filled++;
            }
        }
        
        return $filled . '/' . count($required);
    }

    protected function getDataOrangTuaStatus(): string
    {
        $data = $this->record->dataOrangTua;
        
        if (!$data) {
            return 'Belum diisi';
        }
        
        $required = ['nama_ayah', 'nik_ayah', 'nama_ibu', 'nik_ibu', 'no_hp_ortu'];
        $filled = 0;
        
        foreach ($required as $field) {
            if (filled($data->$field)) {
                $filled++;
            }
        }
        
        return $filled . '/' . count($required);
    }

    protected function getSekolahAsalStatus(): string
    {
        $data = $this->record->dataSekolahAsal;
        
        if (!$data) {
            return 'Belum diisi';
        }
        
        $required = ['nama_sekolah', 'npsn', 'alamat_sekolah', 'kota_sekolah', 'provinsi_sekolah'];
        $filled = 0;
        
        foreach ($required as $field) {
            if (filled($data->$field)) {
                $filled++;
            }
        }
        
        return $filled . '/' . count($required);
    }

    protected function getNilaiRaportStatus(): string
    {
        $count = $this->record->nilaiRaport()->count();
        return $count . '/4 semester';
    }

    protected function getPrestasiOrganisasiStatus(): string
    {
        $prestasiCount = $this->record->prestasi()->count();
        $organisasi = $this->record->organisasi;
        
        return $prestasiCount . ' prestasi, ' . ($organisasi ? '1' : '0') . ' organisasi';
    }

    public function isAllDataComplete(): bool
    {
        return ($this->data['data_pribadi_ok'] ?? false) &&
               ($this->data['data_orang_tua_ok'] ?? false) &&
               ($this->data['sekolah_asal_ok'] ?? false) &&
               ($this->data['nilai_raport_ok'] ?? false) &&
               ($this->data['prestasi_organisasi_ok'] ?? false);
    }
}