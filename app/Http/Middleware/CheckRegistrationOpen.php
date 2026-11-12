<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Notifications\Notification;

class CheckRegistrationOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (AppSetting::isRegistrationClosed()) {
            Notification::make()
                ->title('Pendaftaran Telah Ditutup')
                ->body(AppSetting::getClosedMessage())
                ->danger()
                ->send();

            return redirect()->route('filament.pendaftar.pages.dashboard');
        }

        return $next($request);
    }
}
