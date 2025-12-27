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

    protected static ?string $navigationLabel = 'Penerimaan';

    protected static ?string $modelLabel = 'Penerimaan';

    protected static ?string $pluralModelLabel = 'Penerimaan';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor_pendaftaran')
                    ->label('Nomor Pendaftaran')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('pendaftaran_id')
                    ->label('Pendaftaran')
                    ->relationship('pendaftaran', 'nomor_pendaftaran')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'diterima' => 'Diterima',
                        'tidak_diterima' => 'Tidak Diterima',
                        'cadangan' => 'Cadangan',
                    ])
                    ->required()
                    ->default('diterima'),
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
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('pendaftaran.user.nisn')
                    ->label('NISN')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'diterima',
                        'danger' => 'tidak_diterima',
                        'warning' => 'cadangan',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'diterima' => 'Diterima',
                        'tidak_diterima' => 'Tidak Diterima',
                        'cadangan' => 'Cadangan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('lastUpdatedBy.name')
                    ->label('Terakhir Diupdate Oleh')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'diterima' => 'Diterima',
                        'tidak_diterima' => 'Tidak Diterima',
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
            'index' => Pages\ListPenerimaans::route('/'),
            'create' => Pages\CreatePenerimaan::route('/create'),
            'edit' => Pages\EditPenerimaan::route('/{record}/edit'),
        ];
    }
}
