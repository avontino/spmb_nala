<?php

namespace App\Filament\Pendaftar\Resources;

use App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;
use App\Models\Pendaftaran;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;

    // Sembunyikan menu resource default agar tidak double
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Form Pendaftaran Saya';
    
    protected static ?string $modelLabel = 'Pendaftaran';
    
    protected static ?string $pluralModelLabel = 'Pendaftaran';

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
                    ->icon('heroicon-o-document-text'),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'submitted',
                        'danger' => 'needs_revision',
                        'success' => 'approved',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitted' => 'Menunggu Verifikasi',
                        'needs_revision' => 'Perlu Perbaikan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
                    
                // ✅ KOLOM KELENGKAPAN DATA
                Tables\Columns\TextColumn::make('completion')
                    ->label('Kelengkapan')
                    ->getStateUsing(fn (Pendaftaran $record) => $record->getCompletionPercentage() . '%')
                    ->badge()
                    ->color(fn (Pendaftaran $record) => match(true) {
                        $record->getCompletionPercentage() === 100 => 'success',
                        $record->getCompletionPercentage() >= 60 => 'warning',
                        default => 'danger',
                    }),
                    
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Tanggal Submit')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum disubmit'),
            ])
            ->actions([
                // ✅ SUBMIT BUTTON - Hanya muncul jika bisa submit
                Tables\Actions\Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->url(fn (Pendaftaran $record) => static::getUrl('submit', ['record' => $record]))
                    ->visible(fn (Pendaftaran $record) => $record->canSubmit()),
                    
                // ✅ EDIT BUTTON - Hanya muncul untuk draft & needs_revision
                Tables\Actions\Action::make('edit')
                    ->label('Isi/Edit Data')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->url(fn (Pendaftaran $record) => static::getUrl('edit-data-pribadi', ['record' => $record]))
                    ->visible(fn (Pendaftaran $record) => in_array($record->status, ['draft', 'needs_revision'])),

                // ✅ CETAK BUKTI BUTTON - Hanya muncul setelah submit
                Tables\Actions\Action::make('cetak_bukti')
                    ->label('Cetak Bukti')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (Pendaftaran $record) => static::getUrl('cetak-bukti', ['record' => $record]))
                    ->visible(fn (Pendaftaran $record) => $record->canPrintProof()),
                
                // ✅ VIEW BUTTON - Untuk status submitted/approved (readonly mode)
                Tables\Actions\Action::make('view')
                    ->label('Lihat Data')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Pendaftaran $record) => static::getUrl('edit-data-pribadi', ['record' => $record]))
                    ->visible(fn (Pendaftaran $record) => in_array($record->status, ['submitted', 'approved', 'rejected']))
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('user_id', auth()->id());
            });
    }

    /**
     * ✅ UPDATE: Method getPages() dengan halaman submit
     */
public static function getPages(): array
{
    return [
        'index' => Pages\ListPendaftaran::route('/'),

        // ❌ HAPUS route 'edit' karena konflik dengan 'edit-data-pribadi'
        // 'edit' => Pages\EditDataPribadi::route('/{record}/edit'),
        
        // ✅ 5 HALAMAN FORM dengan route yang UNIK
        'edit-data-pribadi' => Pages\EditDataPribadi::route('/{record}/data-pribadi'),
        'edit-data-orang-tua' => Pages\EditDataOrangTua::route('/{record}/data-orang-tua'), // ✅ PERBAIKI: ganti 'edit-data-orang-tua' menjadi 'data-orang-tua'
        'edit-sekolah-asal' => Pages\EditDataSekolahAsal::route('/{record}/sekolah-asal'),
        'edit-nilai-raport' => Pages\EditNilaiRaport::route('/{record}/nilai-raport'),
        'edit-prestasi' => Pages\EditPrestasiOrganisasi::route('/{record}/prestasi'),
        
        // ✅ HALAMAN SUBMIT BARU
        'submit' => Pages\SubmitPendaftaran::route('/{record}/submit'),
        
        // ✅ HALAMAN CETAK BUKTI
        'cetak-bukti' => Pages\CetakBuktiPendaftaran::route('/{record}/cetak-bukti'),
    ];
}
    
    public static function getNavigationBadge(): ?string
    {
        $pendaftaran = static::getModel()::where('user_id', auth()->id())->first();
        
        if (!$pendaftaran) {
            return null;
        }
        
        if ($pendaftaran->status === 'needs_revision') {
            return 'Perlu Perbaikan';
        }
        
        if ($pendaftaran->status === 'draft') {
            return $pendaftaran->getCompletionPercentage() . '% Lengkap';
        }
        
        return null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $pendaftaran = static::getModel()::where('user_id', auth()->id())->first();
        
        if (!$pendaftaran) {
            return null;
        }
        
        return match($pendaftaran->status) {
            'needs_revision' => 'danger',
            'draft' => 'warning',
            default => null,
        };
    }
}