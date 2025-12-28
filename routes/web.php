<?php

// Add this route to your routes/web.php file

use App\Http\Controllers\CetakBuktiController;
use Illuminate\Support\Facades\Route;

// Route for downloading exports
Route::middleware(['auth'])->group(function () {
    Route::get('/exports/download/{file}', function ($file) {
        $filePath = storage_path('app/public/exports/' . $file);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
    })->name('exports.download');
});

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

// Route for downloading panduan (only for "lolos" users)
Route::middleware(['auth'])->group(function () {
    Route::get('/download-panduan-tahap-2', function () {
        // Get user's pendaftaran
        $pendaftaran = \App\Models\Pendaftaran::where('user_id', auth()->id())
            ->with('penerimaan')
            ->first();

        // Validasi: User harus punya pendaftaran
        if (!$pendaftaran) {
            abort(403, 'Anda belum memiliki pendaftaran.');
        }

        // Validasi: Harus ada data penerimaan
        if (!$pendaftaran->penerimaan) {
            abort(403, 'Anda belum memiliki data penerimaan.');
        }

        // Validasi: Status penerimaan harus "lolos"
        if ($pendaftaran->penerimaan->status !== 'lolos') {
            abort(403, 'Maaf, file panduan hanya dapat diakses oleh peserta yang dinyatakan lolos seleksi tahap 1.');
        }

        // Download file
        $fileName = 'PENGUMUMAN_PPDB_SMAN_TARUNA_NALA_JAWA_TIMUR_TAHAP_I_TAHUN_AJARAN_2026_2027_&_LAMPIRAN_DENAH_KEDATANGAN.pdf';
        // Path file panduan (simpan di storage/app/private/panduan)
        // File TIDAK disimpan di public agar tidak bisa diakses langsung
        $filePath = storage_path('app/private/'. $fileName);

        // Validasi: File harus ada
        if (!file_exists($filePath)) {
            abort(404, 'File panduan tidak ditemukan. Silakan hubungi panitia.');
        }

        // Log aktivitas download
        \Log::info('Panduan Downloaded', [
            'user_id' => auth()->id(),
            'pendaftaran_id' => $pendaftaran->id,
            'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
        ]);

        

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    })->name('download-panduan-tahap-2');
});