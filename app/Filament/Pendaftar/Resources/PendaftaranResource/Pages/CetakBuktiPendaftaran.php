<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CetakBuktiPendaftaran extends Page
{
    protected static string $resource = PendaftaranResource::class;

    protected static string $view = 'filament.pendaftar.pages.cetak-bukti-pendaftaran';
    
    protected static ?string $title = '📄 Cetak Bukti Pendaftaran';
    
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    
    public $record;

    public function mount(int | string $record): void
    {
        // ✅ Convert ID ke model object
        $this->record = \App\Models\Pendaftaran::findOrFail($record);
        
        // Load semua relasi yang dibutuhkan
        $this->record->load([
            'user',
            'dataPribadi',
            'dataOrangTua',
            'dataSekolahAsal',
        ]);
        
        // Check if submitted
        if ($this->record->status === 'draft') {
            Notification::make()
                ->title('⚠️ Belum Bisa Cetak')
                ->warning()
                ->body('Anda harus submit pendaftaran terlebih dahulu sebelum mencetak bukti pendaftaran.')
                ->send();
                
            redirect()->to(PendaftaranResource::getUrl('index'))->send();
            exit;
        }
        
        // Check if data pribadi complete (especially foto)
        if (!$this->record->dataPribadi || !$this->record->dataPribadi->pas_foto) {
            Notification::make()
                ->title('⚠️ Data Belum Lengkap')
                ->warning()
                ->body('Pas foto belum diupload. Silakan lengkapi data pribadi terlebih dahulu.')
                ->send();

            redirect()->to(PendaftaranResource::getUrl('edit-data-pribadi', ['record' => $this->record]))->send();
            exit;
        }
    }

    public function downloadPdf()
    {
        try {
            // Prepare data
            $data = [
                'pendaftaran' => $this->record,
                'user' => $this->record->user,
                'dataPribadi' => $this->record->dataPribadi,
                'dataOrangTua' => $this->record->dataOrangTua,
                'dataSekolahAsal' => $this->record->dataSekolahAsal,
                'tanggal_cetak' => now()->format('d F Y H:i'),
            ];
            
            // Get foto path
            if ($this->record->dataPribadi->pas_foto) {
                $fotoPath = storage_path('app/public/' . $this->record->dataPribadi->pas_foto);
                
                if (file_exists($fotoPath)) {
                    // Convert image to base64 untuk embed di PDF
                    $imageData = base64_encode(file_get_contents($fotoPath));
                    $imageMime = mime_content_type($fotoPath);
                    $data['foto_base64'] = 'data:' . $imageMime . ';base64,' . $imageData;
                } else {
                    $data['foto_base64'] = null;
                }
            }
            
            // Generate PDF
            $pdf = Pdf::loadView('pdf.bukti-pendaftaran', $data);
            
            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');
            
            // Filename
            $filename = 'Bukti-Pendaftaran-' . $this->record->nomor_pendaftaran . '.pdf';
            
            // Log activity
            \Illuminate\Support\Facades\Log::info('📄 Bukti pendaftaran downloaded', [
                'user_id' => auth()->id(),
                'pendaftaran_id' => $this->record->id,
                'nomor_pendaftaran' => $this->record->nomor_pendaftaran,
            ]);
            
            // Download PDF
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ Error generating PDF: ' . $e->getMessage());
            
            Notification::make()
                ->title('❌ Gagal Generate PDF')
                ->danger()
                ->body('Terjadi kesalahan saat membuat PDF. Silakan coba lagi.')
                ->send();
                
            return null;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_pdf')
                ->label('📥 Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('downloadPdf')
                ->requiresConfirmation()
                ->modalHeading('Download Bukti Pendaftaran')
                ->modalDescription('Pastikan data Anda sudah benar sebelum download. File PDF akan tersimpan di komputer Anda.')
                ->modalSubmitActionLabel('Download'),
                
            \Filament\Actions\Action::make('back')
                ->label('← Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => PendaftaranResource::getUrl('index')),
        ];
    }
}