<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewPendaftaran extends ViewRecord
{
    protected static string $resource = PendaftaranResource::class;

    protected static ?string $title = 'Detail Pendaftaran';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pendaftaran')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('nomor_pendaftaran')
                                    ->label('Nomor Pendaftaran')
                                    ->badge()
                                    ->color('success')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'secondary',
                                        'submitted' => 'warning',
                                        'needs_revision' => 'danger',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'draft' => 'Draft',
                                        'submitted' => 'Menunggu Verifikasi',
                                        'needs_revision' => 'Perlu Perbaikan',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => $state,
                                    }),

                                TextEntry::make('submitted_at')
                                    ->label('Tanggal Submit')
                                    ->dateTime('d F Y, H:i')
                                    ->placeholder('Belum disubmit'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Data Pribadi')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('dataPribadi.nama_lengkap')
                                        ->label('Nama Lengkap')
                                        ->weight(FontWeight::Bold)
                                        ->size(TextEntry\TextEntrySize::Large),

                                    TextEntry::make('user.nik')
                                        ->label('NIK')
                                        ->copyable()
                                        ->copyMessage('NIK disalin!')
                                        ->copyMessageDuration(1500),

                                    TextEntry::make('user.nisn')
                                        ->label('NISN')
                                        ->copyable()
                                        ->copyMessage('NISN disalin!')
                                        ->copyMessageDuration(1500),

                                    TextEntry::make('dataPribadi.jenis_kelamin')
                                        ->label('Jenis Kelamin')
                                        ->badge()
                                        ->color(fn (string $state): string => $state === 'Laki-laki' ? 'info' : 'danger'),

                                    TextEntry::make('dataPribadi.tempat_lahir')
                                        ->label('Tempat Lahir'),

                                    TextEntry::make('dataPribadi.tanggal_lahir')
                                        ->label('Tanggal Lahir')
                                        ->date('d F Y'),

                                    TextEntry::make('dataPribadi.agama')
                                        ->label('Agama')
                                        ->badge(),

                                    TextEntry::make('dataPribadi.golongan_darah')
                                        ->label('Golongan Darah')
                                        ->badge()
                                        ->color('danger'),

                                    TextEntry::make('dataPribadi.tinggi_badan')
                                        ->label('Tinggi Badan')
                                        ->suffix(' cm'),

                                    TextEntry::make('dataPribadi.berat_badan')
                                        ->label('Berat Badan')
                                        ->suffix(' kg'),

                                    TextEntry::make('dataPribadi.status_dalam_keluarga')
                                        ->label('Status dalam Keluarga'),

                                    TextEntry::make('dataPribadi.hobi')
                                        ->label('Hobi')
                                        ->placeholder('Tidak diisi'),

                                    TextEntry::make('dataPribadi.riwayat_penyakit')
                                        ->label('Riwayat Penyakit')
                                        ->columnSpanFull()
                                        ->placeholder('Tidak ada'),

                                    TextEntry::make('dataPribadi.alamat_lengkap')
                                        ->label('Alamat Lengkap')
                                        ->columnSpanFull(),

                                    TextEntry::make('dataPribadi.desa_kelurahan')
                                        ->label('Desa/Kelurahan'),

                                    TextEntry::make('dataPribadi.kecamatan')
                                        ->label('Kecamatan'),

                                    TextEntry::make('dataPribadi.kota_kabupaten')
                                        ->label('Kota/Kabupaten'),

                                    TextEntry::make('dataPribadi.provinsi')
                                        ->label('Provinsi'),

                                    TextEntry::make('dataPribadi.no_hp')
                                        ->label('No. HP/WhatsApp')
                                        ->icon('heroicon-o-phone')
                                        ->copyable()
                                        ->copyMessage('Nomor HP disalin!'),

                                    TextEntry::make('dataPribadi.pilihan_jurusan')
                                        ->label('Pilihan Jurusan')
                                        ->placeholder('Belum diisi'),
                                ]),

                            ImageEntry::make('dataPribadi.pas_foto')
                                ->label('Pas Foto')
                                ->height(200)
                                ->grow(false),
                        ])->from('md'),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Data Orang Tua')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Data Ayah
                                Group::make()
                                    ->schema([
                                        Section::make('Data Ayah')
                                            ->schema([
                                                TextEntry::make('dataOrangTua.nama_ayah')
                                                    ->label('Nama Ayah')
                                                    ->weight(FontWeight::Bold),

                                                TextEntry::make('dataOrangTua.status_ayah')
                                                    ->label('Status')
                                                    ->badge()
                                                    ->color(fn (string $state): string => 
                                                        $state === 'Masih Hidup' ? 'success' : 'secondary'
                                                    ),

                                                TextEntry::make('dataOrangTua.pendidikan_ayah')
                                                    ->label('Pendidikan'),

                                                TextEntry::make('dataOrangTua.pekerjaan_ayah')
                                                    ->label('Pekerjaan'),

                                                TextEntry::make('dataOrangTua.penghasilan_ayah')
                                                    ->label('Penghasilan'),
                                            ])
                                            ->columns(1),
                                    ]),

                                // Data Ibu
                                Group::make()
                                    ->schema([
                                        Section::make('Data Ibu')
                                            ->schema([
                                                TextEntry::make('dataOrangTua.nama_ibu')
                                                    ->label('Nama Ibu')
                                                    ->weight(FontWeight::Bold),

                                                TextEntry::make('dataOrangTua.status_ibu')
                                                    ->label('Status')
                                                    ->badge()
                                                    ->color(fn (string $state): string => 
                                                        $state === 'Masih Hidup' ? 'success' : 'secondary'
                                                    ),

                                                TextEntry::make('dataOrangTua.pendidikan_ibu')
                                                    ->label('Pendidikan'),

                                                TextEntry::make('dataOrangTua.pekerjaan_ibu')
                                                    ->label('Pekerjaan'),

                                                TextEntry::make('dataOrangTua.penghasilan_ibu')
                                                    ->label('Penghasilan'),
                                            ])
                                            ->columns(1),
                                    ]),

                                // Data Wali
                                TextEntry::make('dataOrangTua.nama_wali')
                                    ->label('Nama Wali')
                                    ->placeholder('Tidak ada wali')
                                    ->columnSpanFull(),

                                TextEntry::make('dataOrangTua.pendidikan_wali')
                                    ->label('Pendidikan Wali')
                                    ->placeholder('Tidak ada wali')
                                    ->visible(fn ($record) => $record->dataOrangTua?->nama_wali),

                                TextEntry::make('dataOrangTua.pekerjaan_wali')
                                    ->label('Pekerjaan Wali')
                                    ->placeholder('Tidak ada wali')
                                    ->visible(fn ($record) => $record->dataOrangTua?->nama_wali),

                                TextEntry::make('dataOrangTua.no_hp_ortu')
                                    ->label('No. HP Orang Tua')
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->copyMessage('Nomor HP disalin!'),

                                TextEntry::make('dataOrangTua.total_pemasukan')
                                    ->label('Total Pemasukan')
                                    ->money('IDR'),

                                TextEntry::make('dataOrangTua.total_pengeluaran')
                                    ->label('Total Pengeluaran')
                                    ->money('IDR'),
                            ]),

                        Section::make('Dokumen Orang Tua')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('dataOrangTua.file_kk_ktp')
                                            ->label('KK + KTP Orang Tua')
                                            ->formatStateUsing(fn ($state) => $state ? '📄 File tersedia' : 'Belum upload')
                                            ->color(fn ($state) => $state ? 'success' : 'danger')
                                            ->badge()
                                            ->url(fn ($state) => $state ? asset('storage/' . $state) : null, shouldOpenInNewTab: true),

                                        TextEntry::make('dataOrangTua.file_ktp_wali')
                                            ->label('KTP Wali')
                                            ->formatStateUsing(fn ($state) => $state ? '📄 File tersedia' : 'Tidak ada')
                                            ->color(fn ($state) => $state ? 'success' : 'secondary')
                                            ->badge()
                                            ->url(fn ($state) => $state ? asset('storage/' . $state) : null, shouldOpenInNewTab: true),

                                        TextEntry::make('dataOrangTua.file_slip_gaji')
                                            ->label('Slip Gaji')
                                            ->formatStateUsing(fn ($state) => $state ? '📄 File tersedia' : 'Belum upload')
                                            ->color(fn ($state) => $state ? 'success' : 'danger')
                                            ->badge()
                                            ->url(fn ($state) => $state ? asset('storage/' . $state) : null, shouldOpenInNewTab: true),
                                    ]),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make('Data Sekolah Asal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('dataSekolahAsal.jenjang_sekolah')
                                    ->label('Jenjang')
                                    ->badge(),

                                TextEntry::make('dataSekolahAsal.nama_sekolah')
                                    ->label('Nama Sekolah')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('dataSekolahAsal.status_sekolah')
                                    ->label('Status Sekolah')
                                    ->badge()
                                    ->color(fn (string $state): string => 
                                        $state === 'Negeri' ? 'success' : 'info'
                                    ),

                                TextEntry::make('dataSekolahAsal.alamat_sekolah')
                                    ->label('Alamat Sekolah')
                                    ->columnSpanFull(),

                                TextEntry::make('dataSekolahAsal.file_transkrip_nilai')
                                    ->label('Transkrip Nilai')
                                    ->formatStateUsing(fn ($state) => $state ? '📄 File tersedia' : 'Belum upload')
                                    ->color(fn ($state) => $state ? 'success' : 'danger')
                                    ->badge()
                                    ->url(fn ($state) => $state ? asset('storage/' . $state) : null, shouldOpenInNewTab: true),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make('Nilai Raport (Semester 1-5)')
                    ->schema([
                        RepeatableEntry::make('nilaiRaport')
                            ->label('')
                            ->schema([
                                Grid::make(6)
                                    ->schema([
                                        TextEntry::make('semester')
                                            ->label('Semester')
                                            ->badge()
                                            ->color('info')
                                            ->formatStateUsing(fn ($state) => 'Semester ' . $state)
                                            ->columnSpan(2),

                                        TextEntry::make('rata_rata')
                                            ->label('Rata-rata')
                                            ->badge()
                                            ->color('success')
                                            ->weight(FontWeight::Bold)
                                            ->numeric(decimalPlaces: 2)
                                            ->columnSpan(2),
                                    ]),

                                Grid::make(5)
                                    ->schema([
                                        TextEntry::make('nilai_agama')
                                            ->label('Agama')
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('nilai_ppkn')
                                            ->label('PPKN')
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('nilai_bahasa_indonesia')
                                            ->label('B. Indonesia')
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('nilai_matematika')
                                            ->label('Matematika')
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('nilai_ipa')
                                            ->label('IPA')
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('nilai_ips')
                                            ->label('IPS')
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('nilai_bahasa_inggris')
                                            ->label('B. Inggris')
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('nilai_seni_budaya')
                                            ->label('Seni Budaya')
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('nilai_pjok')
                                            ->label('PJOK')
                                            ->numeric(decimalPlaces: 2),

                                        TextEntry::make('nilai_prakarya')
                                            ->label('Prakarya')
                                            ->numeric(decimalPlaces: 2),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make('Prestasi & Organisasi')
                    ->schema([
                        RepeatableEntry::make('prestasi')
                            ->label('Prestasi')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('nama_prestasi')
                                            ->label('Nama Prestasi')
                                            ->weight(FontWeight::Bold)
                                            ->columnSpan(2),

                                        TextEntry::make('tingkat_prestasi')
                                            ->label('Tingkat')
                                            ->badge()
                                            ->color('warning'),

                                        TextEntry::make('file_bukti_prestasi')
                                            ->label('Bukti Prestasi')
                                            ->formatStateUsing(fn ($state) => $state ? '📄 Lihat Bukti' : 'Tidak ada')
                                            ->color(fn ($state) => $state ? 'success' : 'danger')
                                            ->badge()
                                            ->url(fn ($state) => $state ? asset('storage/' . $state) : null, shouldOpenInNewTab: true)
                                            ->columnSpan(3),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->prestasi->count() > 0),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('organisasi.nama_organisasi')
                                    ->label('Nama Organisasi')
                                    ->placeholder('Tidak ada')
                                    ->columnSpan(2),

                                TextEntry::make('organisasi.jabatan')
                                    ->label('Jabatan')
                                    ->badge()
                                    ->placeholder('Tidak ada'),

                                TextEntry::make('organisasi.file_bukti_organisasi')
                                    ->label('Bukti Organisasi')
                                    ->formatStateUsing(fn ($state) => $state ? '📄 Lihat Bukti' : 'Tidak ada')
                                    ->color(fn ($state) => $state ? 'success' : 'secondary')
                                    ->badge()
                                    ->url(fn ($state) => $state ? asset('storage/' . $state) : null, shouldOpenInNewTab: true)
                                    ->columnSpan(3),
                            ]),

                        TextEntry::make('dokumenPendukung.file_transkrip_prestasi')
                            ->label('Transkrip Prestasi')
                            ->formatStateUsing(fn ($state) => $state ? '📄 File tersedia' : 'Belum upload')
                            ->color(fn ($state) => $state ? 'success' : 'secondary')
                            ->badge()
                            ->url(fn ($state) => $state ? asset('storage/' . $state) : null, shouldOpenInNewTab: true),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make('Catatan Verifikasi')
                    ->schema([
                        RepeatableEntry::make('verifikasi')
                            ->label('')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'approved' => 'success',
                                                'needs_revision' => 'warning',
                                                'rejected' => 'danger',
                                                default => 'secondary',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'approved' => 'Disetujui',
                                                'needs_revision' => 'Perlu Perbaikan',
                                                'rejected' => 'Ditolak',
                                                default => $state,
                                            }),

                                        TextEntry::make('created_at')
                                            ->label('Tanggal')
                                            ->dateTime('d F Y, H:i'),

                                        TextEntry::make('catatan')
                                            ->label('Catatan dari Verifikator')
                                            ->placeholder('Tidak ada catatan')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->verifikasi->count() > 0)
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Cetak Bukti Pendaftaran')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn ($record) => $record->status !== 'draft')
                ->url(fn ($record) => route('pendaftaran.print', $record))
                ->openUrlInNewTab(),

            Actions\Action::make('submit')
                ->label('Submit untuk Verifikasi')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Submit Pendaftaran')
                ->modalDescription('Apakah Anda yakin ingin mengirim pendaftaran ini untuk diverifikasi?')
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
                }),

            Actions\EditAction::make()
                ->visible(fn ($record) => in_array($record->status, ['draft', 'needs_revision'])),
        ];
    }
}