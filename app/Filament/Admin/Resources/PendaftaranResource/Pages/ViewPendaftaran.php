<?php

namespace App\Filament\Admin\Resources\PendaftaranResource\Pages;

use App\Filament\Admin\Resources\PendaftaranResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPendaftaran extends ViewRecord
{
    protected static string $resource = PendaftaranResource::class;

    protected static ?string $title = '👁️ Detail Pendaftaran';

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Load all relationships
        $this->record->load([
            'dataPribadi',
            'dataOrangTua',
            'dataSekolahAsal',
            'nilaiRaport',
            'prestasi',
            'organisasi',
            'latestVerifikasi.verifikator'
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Status Verifikasi')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'needs_revision' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'approved' => '✅ Disetujui',
                                        'rejected' => '❌ Ditolak',
                                        'needs_revision' => '⚠️ Perlu Perbaikan',
                                        default => $state,
                                    }),
                                    
                                TextEntry::make('latestVerifikasi.verifikator.name')
                                    ->label('Diverifikasi Oleh')
                                    ->default('-')
                                    ->placeholder('-'),
                                    
                                TextEntry::make('latestVerifikasi.verified_at')
                                    ->label('Tanggal Verifikasi')
                                    ->dateTime('d M Y, H:i')
                                    ->default('-')
                                    ->placeholder('-'),
                            ]),
                            
                        TextEntry::make('latestVerifikasi.catatan_revisi')
                            ->label('⚠️ Catatan untuk Pendaftar')
                            ->default('-')
                            ->placeholder('-')
                            ->visible(fn () => $this->record->latestVerifikasi && $this->record->latestVerifikasi->catatan_revisi)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->visible(fn () => in_array($this->record->status, ['approved', 'rejected', 'needs_revision'])),

                Section::make('Data Pribadi')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('dataPribadi.pas_foto')
                                    ->label('Pas Foto')
                                    ->height(150)
                                    ->defaultImageUrl(url('/images/avatar.png')),
                                    
                                Group::make([
                                    TextEntry::make('nomor_pendaftaran')
                                        ->label('No. Pendaftaran'),
                                    TextEntry::make('dataPribadi.nama_lengkap')
                                        ->label('Nama Lengkap'),
                                    TextEntry::make('dataPribadi.jenis_kelamin')
                                        ->label('Jenis Kelamin'),
                                ])
                                ->columnSpan(2),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('dataPribadi.tempat_lahir')
                                    ->label('Tempat Lahir'),
                                TextEntry::make('dataPribadi.tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->date('d M Y'),
                                TextEntry::make('dataPribadi.agama')
                                    ->label('Agama'),
                                TextEntry::make('dataPribadi.no_hp')
                                    ->label('No. HP'),
                            ]),
                            
                        TextEntry::make('dataPribadi.alamat_lengkap')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Data Orang Tua')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('dataOrangTua.nama_ayah')
                                    ->label('Nama Ayah'),
                                TextEntry::make('dataOrangTua.pekerjaan_ayah')
                                    ->label('Pekerjaan Ayah'),
                                TextEntry::make('dataOrangTua.nama_ibu')
                                    ->label('Nama Ibu'),
                                TextEntry::make('dataOrangTua.pekerjaan_ibu')
                                    ->label('Pekerjaan Ibu'),
                                TextEntry::make('dataOrangTua.no_hp_ortu')
                                    ->label('No. HP Orang Tua')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Sekolah Asal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('dataSekolahAsal.nama_sekolah')
                                    ->label('Nama Sekolah'),
                                TextEntry::make('dataSekolahAsal.jenjang_sekolah')
                                    ->label('Jenjang'),
                                TextEntry::make('dataSekolahAsal.status_sekolah')
                                    ->label('Status'),
                                TextEntry::make('dataSekolahAsal.alamat_sekolah')
                                    ->label('Alamat Sekolah')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Nilai Raport')
                    ->schema([
                        RepeatableEntry::make('nilaiRaport')
                            ->label('')
                            ->schema([
                                TextEntry::make('semester')
                                    ->label('Semester')
                                    ->badge(),
                                TextEntry::make('rata_rata')
                                    ->label('Rata-rata')
                                    ->numeric(2)
                                    ->badge()
                                    ->color('success'),
                                Grid::make(5)
                                    ->schema([
                                        TextEntry::make('nilai_matematika')->label('Matematika'),
                                        TextEntry::make('nilai_bahasa_indonesia')->label('B. Indonesia'),
                                        TextEntry::make('nilai_bahasa_inggris')->label('B. Inggris'),
                                        TextEntry::make('nilai_ipa')->label('IPA'),
                                        TextEntry::make('nilai_ips')->label('IPS'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->visible(fn () => $this->record->nilaiRaport->count() > 0),

                Section::make('Prestasi')
                    ->schema([
                        RepeatableEntry::make('prestasi')
                            ->label('')
                            ->schema([
                                TextEntry::make('nama_prestasi')
                                    ->label('Nama Prestasi'),
                                TextEntry::make('tingkat_prestasi')
                                    ->label('Tingkat')
                                    ->badge(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->visible(fn () => $this->record->prestasi->count() > 0),

                Section::make('Organisasi')
                    ->schema([
                        RepeatableEntry::make('organisasi')
                            ->label('Daftar Organisasi')
                            ->schema([
                                TextEntry::make('nama_organisasi')
                                    ->label('Nama Organisasi'),
                                TextEntry::make('jabatan')
                                    ->label('Jabatan'),
                                TextEntry::make('periode_jabatan')
                                    ->label('Periode Jabatan')
                                    ->placeholder('-'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->visible(fn () => $this->record->organisasi()->exists()),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Cetak')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('cetak-pendaftaran', ['record' => $this->record->id]))
                ->openUrlInNewTab(),
                
            Actions\Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => PendaftaranResource::getUrl('index')),
        ];
    }
}