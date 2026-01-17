<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PenerimaanTahap2Resource\Pages;
use App\Filament\Admin\Resources\PenerimaanTahap2Resource\RelationManagers;
use App\Models\PenerimaanTahap2;
use App\Models\Penerimaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenerimaanTahap2Resource extends Resource
{
    protected static ?string $model = PenerimaanTahap2::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Penerimaan Tahap 2';

    protected static ?string $modelLabel = 'Penerimaan Tahap 2';

    protected static ?string $pluralModelLabel = 'Penerimaan Tahap 2';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('penerimaan_id')
                    ->label('Penerimaan Tahap 1')
                    ->relationship(
                        'penerimaan',
                        'nomor_pendaftaran',
                        fn ($query, $record) => $query->where('status', 'lolos')
                            ->where(function ($q) use ($record) {
                                $q->whereDoesntHave('penerimaanTahap2');
                                if ($record) {
                                    $q->orWhere('id', $record->penerimaan_id);
                                }
                            })
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nomor_pendaftaran . ' - ' . ($record->pendaftaran?->dataPribadi?->nama_lengkap ?? $record->pendaftaran?->user?->name ?? '-'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->unique(table: 'penerimaan_tahap2', column: 'penerimaan_id', ignoreRecord: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('nomor_pendaftaran', Penerimaan::find($state)?->nomor_pendaftaran)),
                Forms\Components\Hidden::make('nomor_pendaftaran')
                    ->label('Nomor Pendaftaran'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'lolos' => 'Lolos',
                        'tidak_lolos' => 'Tidak Lolos',
                        'cadangan' => 'Cadangan',
                    ])
                    ->required()
                    ->default('lolos'),
                Forms\Components\TextInput::make('nomor_va')
                    ->label('Nomor VA')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sesi')
                    ->label('Sesi')
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('penerimaan.pendaftaran.dataPribadi.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->placeholder(fn ($record) => $record->penerimaan?->pendaftaran?->user?->name ?? '-'),
                Tables\Columns\TextColumn::make('penerimaan.pendaftaran.user.nisn')
                    ->label('NISN')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'lolos',
                        'danger' => 'tidak_lolos',
                        'warning' => 'cadangan',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lolos' => 'Lolos',
                        'tidak_lolos' => 'Tidak Lolos',
                        'cadangan' => 'Cadangan',
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
                        'cadangan' => 'Cadangan',
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
            'index' => Pages\ListPenerimaanTahap2s::route('/'),
            'create' => Pages\CreatePenerimaanTahap2::route('/create'),
            'edit' => Pages\EditPenerimaanTahap2::route('/{record}/edit'),
        ];
    }
}
