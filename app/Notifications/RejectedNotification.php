<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Pendaftaran Anda perlu Ditolak')
                    ->line('Pendaftaran Anda dengan nomor '.$this->data['nomor_pendaftaran'].' telah ditolak.')
                    ->line('Alasan penolakan: '.$this->data['catatan_revisi'])
                    ->action('Lihat Detail', route('filament.pendaftar.resources.pendaftarans.index'))
                    ->line('Kami mohon maaf atas ketidaknyamanannya. Silahkan ajukan pendaftaran ulang jika diperlukan.')
                    ->line('Terima kasih atas perhatiannya.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
