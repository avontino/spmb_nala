<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PenerimaanResource\Pages;
use App\Filament\Admin\Resources\PenerimaanResource\RelationManagers;
use App\Models\Penerimaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenerimaanResource extends Resource
{
    protected static ?string $model = Penerimaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Penerimaan Tahap 1';

    protected static ?string $modelLabel = 'Penerimaan Tahap 1';

    protected static ?string $pluralModelLabel = 'Penerimaan Tahap 1';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pendaftaran_id')
                    ->label('Pendaftaran')
                    ->relationship('pendaftaran', 'nomor_pendaftaran', fn ($query) => $query->where('status', 'approved'))
                    ->searchable()
                    ->preload()
                    ->unique(table: 'penerimaan', column: 'pendaftaran_id', ignoreRecord: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('nomor_pendaftaran', \App\Models\Pendaftaran::find($state)?->nomor_pendaftaran)),
                Forms\Components\Hidden::make('nomor_pendaftaran')
                    ->label('Nomor Pendaftaran'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'lolos' => 'Lolos',
                        'tidak_lolos' => 'Tidak Lolos',
                    ])
                    ->required()
                    ->default('lolos'),
                Forms\Components\TextInput::make('nomor_va')
                    ->label('Nomor VA')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sesi')
                    ->label('Sesi')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sesi_interview')
                    ->label('Sesi Interview Orang Tua')
                    ->placeholder('Contoh: Sesi 1: Rabu, 8 Januari 2026, Pukul 08.00 WIB')
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_pendaftaran')
                    ->label('Nomor Pendaftaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pendaftaran.dataPribadi.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    // if null use user name
                    ->placeholder(fn ($record) => $record->pendaftaran->user->name),
                Tables\Columns\TextColumn::make('pendaftaran.user.nisn')
                    ->label('NISN')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'lolos',
                        'danger' => 'tidak_lolos',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lolos' => 'Lolos',
                        'tidak_lolos' => 'Tidak Lolos',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('nomor_va')
                    ->label('Nomor VA')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('sesi')
                    ->label('Sesi')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('sesi_interview')
                    ->label('Sesi Interview')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('lastUpdatedBy.name')
                    ->label('Terakhir Diupdate Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'lolos' => 'Lolos',
                        'tidak_lolos' => 'Tidak Lolos',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenerimaans::route('/'),
            'create' => Pages\CreatePenerimaan::route('/create'),
            'edit' => Pages\EditPenerimaan::route('/{record}/edit'),
        ];
    }
}
