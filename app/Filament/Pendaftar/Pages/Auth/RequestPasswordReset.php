<?php

namespace App\Filament\Pendaftar\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return;
        }

        $data = $this->form->getState();

        try {
            $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
                $data,
                function (CanResetPassword $user, string $token): void {
                    $notification = app(ResetPasswordNotification::class, ['token' => $token]);
                    $notification->url = Filament::getResetPasswordUrl($token, $user);

                    $user->notify($notification);
                },
            );

            if ($status !== Password::RESET_LINK_SENT) {
                $this->getFailureNotification($status)?->send();
                return;
            }

            $this->getSentNotification($status)?->send();
            $this->form->fill();
        } catch (\Throwable $e) {
            Log::error('Error sending password reset email: ' . $e->getMessage(), [
                'email' => $data['email'] ?? null,
                'exception' => $e,
            ]);

            Notification::make()
                ->title('Gagal Mengirim Email')
                ->body('Terjadi kendala pada server saat mengirim email reset password. Pastikan email Anda benar atau hubungi panitia SPMB.')
                ->danger()
                ->send();
        }
    }
}
