<?php

namespace App\Filament\Admin\Resources\PenerimaanResource\Pages;

use App\Filament\Admin\Resources\PenerimaanResource;
use App\Imports\PenerimaanImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ListPenerimaans extends ListRecords
{
    protected static string $resource = PenerimaanResource::class;

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

                        $import = new PenerimaanImport;

                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $extension = strtolower($file->getClientOriginalExtension());
                            $readerType = $extension === 'xlsx' ? \Maatwebsite\Excel\Excel::XLSX : \Maatwebsite\Excel\Excel::XLS;
                            Excel::import($import, $file->getRealPath(), null, $readerType);
                        } else {
                            $filePath = Storage::disk('local')->path($file);
                            Excel::import($import, $filePath);
                            Storage::disk('local')->delete($file);
                        }

                        Notification::make()
                            ->title('Import selesai')
                            ->body("Berhasil diinsert: {$import->inserted}, Gagal: {$import->failed}")
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal import data')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}
