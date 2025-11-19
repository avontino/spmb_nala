<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class ExportCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $fileName;
    public $filePath;

    /**
     * Create a new notification instance.
     */
    public function __construct($fileName, $filePath)
    {
        $this->fileName = $fileName;
        $this->filePath = $filePath;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the Filament database notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $notification = FilamentNotification::make()
            ->title('Export Completed')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->body('Your export file **' . $this->fileName . '** is ready for download.')
            ->actions([
                Action::make('download')
                    ->button()
                    ->url(route('exports.download', ['file' => basename($this->filePath)]))
                    ->openUrlInNewTab()
                    ->markAsRead(),
            ]);

        return $notification->toArray();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Export Completed',
            'message' => 'Your export file ' . $this->fileName . ' is ready for download.',
            'file_name' => $this->fileName,
            'file_path' => $this->filePath,
        ];
    }
}
