<?php

namespace App\Filament\Admin\Resources\PenerimaanTahap2Resource\Pages;

use App\Filament\Admin\Resources\PenerimaanTahap2Resource;
use App\Imports\PenerimaanTahap2Import;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListPenerimaanTahap2s extends ListRecords
{
    protected static string $resource = PenerimaanTahap2Resource::class;

    protected $listeners = ['refreshPenerimaanTahap2List' => '$refresh'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    $filePath = public_path('files/template-penerimaan-tahap2.xlsx');

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
                        ->helperText('Format: .xlsx atau .xls (maksimal 5MB). Nomor pendaftaran harus sudah lolos di Penerimaan Tahap 1.'),
                ])
                ->action(function (array $data) {
                    try {
                        $file = $data['file'];
                        $importId = uniqid('import_tahap2_', true);
                        $userId = auth()->id();

                        $import = new PenerimaanTahap2Import($importId, $userId);

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
