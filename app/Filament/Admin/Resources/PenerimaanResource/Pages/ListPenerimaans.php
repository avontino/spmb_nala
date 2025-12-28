<?php

namespace App\Filament\Admin\Resources\PenerimaanResource\Pages;

use App\Filament\Admin\Resources\PenerimaanResource;
use App\Imports\PenerimaanImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class ListPenerimaans extends ListRecords
{
    protected static string $resource = PenerimaanResource::class;

    protected $listeners = ['refreshPenerimaanList' => '$refresh'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    $filePath = public_path('files/template-penerimaan.xlsx');

                    if (!file_exists($filePath)) {
                        Notification::make()
                            ->title('Template tidak ditemukan')
                            ->danger()
                            ->send();
                        return;
                    }

                    return response()->download($filePath);
                }),

            Actions\Action::make('importExcel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required()
                        ->disk('local')
                        ->directory('temp-imports')
                        ->visibility('private')
                        ->maxSize(5120)
                        ->storeFiles(false)
                        ->helperText('Format: .xlsx atau .xls (maksimal 5MB)'),
                ])
                ->action(function (array $data) {
                    try {
                        $file = $data['file'];
                        $importId = uniqid('import_', true);
                        $userId = auth()->id();

                        // Langsung import menggunakan PenerimaanImport
                        // Karena implement ShouldQueue, akan otomatis jadi queue job
                        $import = new PenerimaanImport($importId, $userId);

                        Excel::import($import, $file);

                        Notification::make()
                            ->title('Import dimulai')
                            ->body('Import data sedang diproses di background. Anda akan menerima notifikasi ketika selesai.')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal memulai import')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}
