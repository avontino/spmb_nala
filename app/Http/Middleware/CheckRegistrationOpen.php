<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
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
        // Cek apakah ada tanggal penutupan pendaftaran
        $closedDate = config('app.register_closed_date');

        // Jika tidak ada tanggal penutupan, izinkan akses
        if (!$closedDate) {
            return $next($request);
        }

        // Cek apakah sudah melewati tanggal penutupan
        $now = Carbon::now();
        $registerClosedDate = Carbon::parse($closedDate);

        if ($now->gte($registerClosedDate)) {
            // Tampilkan notifikasi
            Notification::make()
                ->title('Pendaftaran Telah Ditutup')
                ->body('Pendaftaran telah ditutup sejak ' . $registerClosedDate->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i') . '. Anda hanya dapat mengakses dashboard.')
                ->danger()
                ->send();

            // Redirect ke dashboard
            return redirect()->route('filament.pendaftar.pages.dashboard');
        }

        return $next($request);
    }
}
