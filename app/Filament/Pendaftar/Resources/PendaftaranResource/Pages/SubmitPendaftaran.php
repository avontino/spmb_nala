<?php

namespace App\Filament\Pendaftar\Resources\PendaftaranResource\Pages;

use App\Filament\Pendaftar\Resources\PendaftaranResource;
use App\Models\AppSetting;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

class SubmitPendaftaran extends EditRecord
{
    protected static string $resource = PendaftaranResource::class;
    
    protected static ?string $title = '🚀 Selesaikan Pendaftaran';
    protected static ?string $navigationLabel = 'Selesaikan Pendaftaran';
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    
    public function getBreadcrumb(): string
    {
        return 'Selesaikan Pendaftaran';
    }

    public function mount(int | string $record): void
    {
        if (AppSetting::isRegistrationClosed()) {
            Notification::make()
                ->title('Pendaftaran Telah Ditutup')
                ->body(AppSetting::getClosedMessage())
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(route('filament.pendaftar.pages.dashboard'), navigate: true);
            return;
        }

        parent::mount($record);

        // Redirect jika tidak bisa submit
        if (!$this->record->canSubmit()) {
            Notification::make()
                ->title('⚠️ Tidak Bisa Submit')
                ->warning()
                ->body('Lengkapi semua data pendaftaran terlebih dahulu.')
                ->persistent()
                ->send();

            $this->redirect(PendaftaranResource::getUrl('index'));
        }
    }

    public function form(Form $form): Form
    {
        $completionStatus = $this->record->getCompletionStatus();
        
        return $form
            ->schema([
                Section::make('📋 Ringkasan Kelengkapan Data')
                    ->description('Pastikan semua data sudah benar sebelum mengirim pendaftaran')
                    ->schema([
                        Placeholder::make('completion_overview')
                            ->label('')
                            ->content(function () use ($completionStatus) {
                                $html = '<div class="space-y-3">';
                                
                                $sections = [
                                    'data_pribadi' => ['label' => 'Data Pribadi', 'icon' => 'user'],
                                    'data_orang_tua' => ['label' => 'Data Orang Tua', 'icon' => 'users'],
                                    'sekolah_asal' => ['label' => 'Sekolah Asal', 'icon' => 'academic-cap'],
                                    'nilai_raport' => ['label' => 'Nilai Raport', 'icon' => 'chart-bar'],
                                    'prestasi_organisasi' => ['label' => 'Prestasi & Organisasi', 'icon' => 'trophy'],
                                ];
                                
                                foreach ($sections as $key => $section) {
                                    $isComplete = $completionStatus[$key];
                                    $statusIcon = $isComplete ? '✅' : '❌';
                                    $statusText = $isComplete ? 'Lengkap' : 'Belum Lengkap';
                                    $statusColor = $isComplete ? 'text-green-600' : 'text-red-600';
                                    
                                    $html .= "
                                        <div class='flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg'>
                                            <div class='flex items-center gap-2'>
                                                <span class='text-lg'>{$statusIcon}</span>
                                                <span class='font-medium'>{$section['label']}</span>
                                            </div>
                                            <span class='{$statusColor} font-semibold'>{$statusText}</span>
                                        </div>
                                    ";
                                }
                                
                                $html .= '</div>';
                                
                                return new HtmlString($html);
                            }),
                    ]),

                Section::make('✅ Konfirmasi Submit')
                    ->schema([
                        Placeholder::make('submit_info')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
                                        📌 Informasi Penting
                                    </h3>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-blue-800 dark:text-blue-200">
                                        <li>Setelah submit, data tidak dapat diubah kecuali diminta revisi oleh verifikator</li>
                                        <li>Pastikan semua data sudah benar dan sesuai dokumen asli</li>
                                        <li>Anda akan mendapat bukti pendaftaran setelah submit</li>
                                        <li>Proses verifikasi memakan waktu 1-3 hari kerja</li>
                                    </ul>
                                </div>
                            ')),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('← Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => PendaftaranResource::getUrl('index')),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('submit_pendaftaran')
                ->label('🚀 Submit Pendaftaran Sekarang')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Submit Pendaftaran')
                ->modalDescription('Apakah Anda yakin ingin mengirim pendaftaran? Data tidak dapat diubah setelah submit kecuali diminta revisi.')
                ->modalSubmitActionLabel('Ya, Submit Sekarang')
                ->modalCancelActionLabel('Batal')
                ->action(function () {
                    $this->submitPendaftaran();
                }),
                
            Actions\Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->url(PendaftaranResource::getUrl('index'))
                ->icon('heroicon-o-x-mark'),
        ];
    }

    protected function submitPendaftaran(): void
    {
        // Double check completeness
        if (!$this->record->canSubmit()) {
            Notification::make()
                ->title('⚠️ Tidak Bisa Submit')
                ->danger()
                ->body('Data pendaftaran belum lengkap.')
                ->send();
            return;
        }

        // Update status
        $this->record->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        Notification::make()
            ->title('🎉 Pendaftaran Berhasil Dikirim!')
            ->success()
            ->body('Pendaftaran Anda telah dikirim untuk diverifikasi. Anda dapat mencetak bukti pendaftaran sekarang.')
            ->persistent()
            ->send();

        // Redirect ke halaman cetak bukti
        $this->redirect(PendaftaranResource::getUrl('cetak-bukti', ['record' => $this->record]));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Tidak perlu menyimpan apapun
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return PendaftaranResource::getUrl('index');
    }
}