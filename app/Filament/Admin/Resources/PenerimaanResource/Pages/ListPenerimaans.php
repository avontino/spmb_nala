<?php

namespace App\Filament\Admin\Resources\PenerimaanResource\Pages;

use App\Filament\Admin\Resources\PenerimaanResource;
use App\Imports\PenerimaanImport;
use App\Jobs\ProcessPenerimaanImport;
use App\Models\ImportLog;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
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
                        $userId = Auth::id();

                        // Simpan file ke storage temporary
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $fileName = 'imports/' . $importId . '_' . $file->getClientOriginalName();
                            $filePath = $file->storeAs('', $fileName, 'local');
                            $originalFileName = $file->getClientOriginalName();
                        } else {
                            // File sudah tersimpan di storage
                            $fileName = 'imports/' . $importId . '_' . basename($file);
                            Storage::disk('local')->move($file, $fileName);
                            $filePath = $fileName;
                            $originalFileName = basename($file);
                        }

                        // Buat import log
                        ImportLog::create([
                            'import_id' => $importId,
                            'user_id' => $userId,
                            'type' => 'penerimaan',
                            'status' => 'queued',
                            'file_name' => $originalFileName,
                        ]);

                        // Dispatch job ke queue
                        ProcessPenerimaanImport::dispatch(
                            Storage::disk('local')->path($filePath),
                            $importId,
                            $userId
                        );

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
