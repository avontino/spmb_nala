<?php

namespace App\Filament\Verifikator\Resources;

use App\Filament\Verifikator\Resources\VerifikasiResource\Pages;
use App\Models\Pendaftaran;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VerifikasiResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?string $navigationLabel = 'Verifikasi Pendaftaran';
    
    protected static ?string $modelLabel = 'Verifikasi';
    
    protected static ?string $pluralModelLabel = 'Verifikasi Pendaftaran';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_pendaftaran')
                    ->label('No. Pendaftaran')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-document-text'),
                    
                Tables\Columns\TextColumn::make('dataPribadi.nama_lengkap')
                    ->label('Nama Pendaftar')
                    ->searchable()
                    ->sortable()
                    ->default('-')
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('dataPribadi.tempat_lahir')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->default('-')
                    ->toggleable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'submitted',
                        'danger' => 'needs_revision',
                        'success' => 'approved',
                        'gray' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-clock' => 'submitted',
                        'heroicon-o-exclamation-triangle' => 'needs_revision',
                        'heroicon-o-check-circle' => 'approved',
                        'heroicon-o-x-circle' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitted' => 'Menunggu Verifikasi',
                        'needs_revision' => 'Perlu Perbaikan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('latestVerifikasi.data_pribadi_ok')
                    ->label('Data Pribadi')
                    ->boolean()
                    ->default(false)
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('latestVerifikasi.data_orang_tua_ok')
                    ->label('Data Ortu')
                    ->boolean()
                    ->default(false)
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('latestVerifikasi.sekolah_asal_ok')
                    ->label('Sekolah')
                    ->boolean()
                    ->default(false)
                    ->toggleable(),

                    Tables\Columns\IconColumn::make('latestVerifikasi.nilai_raport_ok')
                    ->label('Rapor')
                    ->boolean()
                    ->default(false)
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Tgl Submit')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('latestVerifikasi.verifikator.name')
                    ->label('Diverifikasi Oleh')
                    ->default('Belum diverifikasi')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'submitted' => 'Menunggu Verifikasi',
                        'needs_revision' => 'Perlu Perbaikan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    ->url(fn (Pendaftaran $record) => static::getUrl('verifikasi', ['record' => $record]))
                    ->visible(fn (Pendaftaran $record) => in_array($record->status, ['submitted', 'needs_revision'])),
                    
                Tables\Actions\Action::make('lihat')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Pendaftaran $record) => static::getUrl('view', ['record' => $record]))
                    ->visible(fn (Pendaftaran $record) => in_array($record->status, ['approved', 'rejected'])),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->defaultSort('submitted_at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerifikasi::route('/'),
            'verifikasi' => Pages\VerifikasiPendaftaran::route('/{record}/verifikasi'),
            'view' => Pages\ViewPendaftaran::route('/{record}'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'submitted')->count();
        
        return $count > 0 ? (string) $count : null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}