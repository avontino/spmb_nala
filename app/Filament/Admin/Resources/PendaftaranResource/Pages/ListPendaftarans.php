<?php

namespace App\Filament\Admin\Resources\PendaftaranResource\Pages;

use App\Filament\Admin\Resources\PendaftaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PendaftaranExport;
use Filament\Notifications\Notification;

class ListPendaftarans extends ListRecords
{
    protected static string $resource = PendaftaranResource::class;

    protected static ?string $title = 'Verifikasi Pendaftaran';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->redirect($this->getResource()::getUrl('index'))),
            Actions\Action::make('export')
                ->label('Export')
                ->action(function () {
                    $fileName = 'pendaftaran_' . now()->format('Y-m-d_His') . '.xlsx';
                    $filePath = 'exports/' . $fileName;

                    // Capture the user before queuing
                    $user = auth()->user();

                    // Queue the export
                    Excel::queue(new PendaftaranExport, $filePath, 'public')->chain([
                        function () use ($fileName, $filePath, $user) {
                            // Send Filament notification when export is complete
                            \Filament\Notifications\Notification::make()
                                ->title('Export Completed')
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->body('Your export file **' . $fileName . '** is ready for download.')
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('download')
                                        ->button()
                                        ->url(route('exports.download', ['file' => basename($filePath)]))
                                        ->openUrlInNewTab()
                                        ->markAsRead(),
                                ])
                                ->sendToDatabase($user);
                        }
                    ]);

                    // Show success notification
                    Notification::make()
                        ->title('Export Started')
                        ->body('Your export is being processed. You will be notified when it is ready for download.')
                        ->success()
                        ->send();
                })
                ->icon('heroicon-o-document-arrow-down'),
        ];
    }
}
