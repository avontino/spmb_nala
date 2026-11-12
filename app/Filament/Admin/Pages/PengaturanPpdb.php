<?php

namespace App\Filament\Admin\Pages;

use App\Models\AppSetting;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PengaturanPpdb extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan PPDB';

    protected static ?string $title = 'Pengaturan Jadwal PPDB';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.admin.pages.pengaturan-ppdb';

    public ?array $data = [];

    public function mount(): void
    {
        $closedDate = AppSetting::getRegisterClosedDate();

        $this->form->fill([
            'registration_mode' => AppSetting::getRegistrationMode(),
            'register_closed_date' => $closedDate?->format('Y-m-d H:i:s'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Jadwal Buka & Tutup Pendaftaran')
                    ->description('Atur apakah pendaftar dapat mendaftar akun baru serta mengisi/mengubah formulir pendaftaran.')
                    ->schema([
                        Radio::make('registration_mode')
                            ->label('Mode Pendaftaran')
                            ->options([
                                AppSetting::MODE_SCHEDULE => 'Otomatis Sesuai Jadwal Penutupan',
                                AppSetting::MODE_OPEN => 'Selalu Dibuka (Abaikan Tanggal Penutupan)',
                                AppSetting::MODE_CLOSED => 'Tutup Pendaftaran Sekarang',
                            ])
                            ->descriptions([
                                AppSetting::MODE_SCHEDULE => 'Pendaftaran terbuka hingga tanggal & waktu penutupan yang ditentukan di bawah.',
                                AppSetting::MODE_OPEN => 'Pendaftaran dan pengisian form tetap terbuka tanpa batas tanggal.',
                                AppSetting::MODE_CLOSED => 'Menutup registrasi dan seluruh menu/form pendaftar secara langsung (pendaftar hanya melihat Dashboard).',
                            ])
                            ->required()
                            ->live(),

                        DateTimePicker::make('register_closed_date')
                            ->label('Tanggal & Waktu Penutupan Pendaftaran')
                            ->helperText('Digunakan saat mode otomatis aktif. Kosongkan jika tidak ingin membatasi tanggal.')
                            ->seconds(false)
                            ->native(false)
                            ->displayFormat('d M Y H:i')
                            ->visible(fn (Get $get): bool => $get('registration_mode') === AppSetting::MODE_SCHEDULE),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        AppSetting::setValue(
            AppSetting::KEY_REGISTRATION_MODE,
            $state['registration_mode'] ?? AppSetting::MODE_SCHEDULE
        );

        if (array_key_exists('register_closed_date', $state)) {
            AppSetting::setValue(
                AppSetting::KEY_REGISTER_CLOSED_DATE,
                $state['register_closed_date']
            );
        }

        Notification::make()
            ->title('Pengaturan PPDB berhasil disimpan')
            ->success()
            ->send();
    }

    public function isCurrentlyClosed(): bool
    {
        return AppSetting::isRegistrationClosed();
    }

    public function getCurrentClosedDateFormatted(): ?string
    {
        $date = AppSetting::getRegisterClosedDate();

        return $date?->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i');
    }
}
