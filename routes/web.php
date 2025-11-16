<?php

// Add this route to your routes/web.php file

use App\Http\Controllers\CetakBuktiController;
use Illuminate\Support\Facades\Route;

// Route for printing pendaftaran proof
Route::middleware(['auth'])->group(function () {
    Route::get('/cetak-pendaftaran/{record}', function ($record) {
        $pendaftaran = \App\Models\Pendaftaran::findOrFail($record);
        
        // Load relationships
        $pendaftaran->load([
            'user',
            'dataPribadi',
            'dataOrangTua',
            'dataSekolahAsal',
            'latestVerifikasi.verifikator',
        ]);
        
        // Check authorization
        if (auth()->user()->role === 'pendaftar' && $pendaftaran->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Prepare data for PDF
        $data = [
            'pendaftaran' => $pendaftaran,
            'user' => $pendaftaran->user ?? null,
            'dataPribadi' => $pendaftaran->dataPribadi ?? null,
            'dataOrangTua' => $pendaftaran->dataOrangTua ?? null,
            'dataSekolahAsal' => $pendaftaran->dataSekolahAsal ?? null,
            'tanggal_cetak' => now()->format('d F Y H:i'),
            'foto_base64' => null,
        ];
        
        // Get photo (optional)
        if ($pendaftaran->dataPribadi && $pendaftaran->dataPribadi->pas_foto) {
            $fotoPath = storage_path('app/public/' . $pendaftaran->dataPribadi->pas_foto);
            
            if (file_exists($fotoPath)) {
                try {
                    $imageData = base64_encode(file_get_contents($fotoPath));
                    $imageMime = mime_content_type($fotoPath);
                    $data['foto_base64'] = 'data:' . $imageMime . ';base64,' . $imageData;
                } catch (\Exception $e) {
                    \Log::warning('Photo load failed', ['error' => $e->getMessage()]);
                }
            }
        }
        
        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bukti-pendaftaran', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Bukti-Pendaftaran-' . $pendaftaran->nomor_pendaftaran . '.pdf';
        
        // Log
        \Log::info('PDF Downloaded', [
            'user_id' => auth()->id(),
            'pendaftaran_id' => $pendaftaran->id,
        ]);
        
        return $pdf->stream($filename);
    })->name('cetak-pendaftaran');
});