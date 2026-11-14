<?php

namespace App\Filament\Admin\Pages;

use App\Models\AppSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class KelolaPengumumanDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Pengumuman Dashboard';

    protected static ?string $title = 'Kelola Pengumuman & Informasi Dashboard Pendaftar';

    protected static ?int $navigationSort = 97;

    protected static string $view = 'filament.admin.pages.kelola-pengumuman-dashboard';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(AppSetting::getPendaftarDashboardContent());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetDefault')
                ->label('Reset ke Default')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reset Pengumuman Dashboard?')
                ->modalDescription('Pengumuman utama, daftar informasi penting, dan kontak panitia di dashboard pendaftar akan dikembalikan ke isi default.')
                ->action(function (): void {
                    AppSetting::resetPendaftarDashboardContent();
                    $this->form->fill(AppSetting::getPendaftarDashboardContent());

                    Notification::make()
                        ->title('Pengumuman dashboard dikembalikan ke default')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Banner Pengumuman Utama')
                    ->description('Tampil di bagian atas Dashboard Pendaftar. Berguna untuk menyampaikan pengumuman penting, jadwal daftar ulang, atau informasi mendadak.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('announcement_enabled')
                            ->label('Tampilkan Banner Pengumuman di Dashboard Pendaftar'),
                        Select::make('announcement_type')
                            ->label('Warna / Jenis Pengumuman')
                            ->options([
                                'info' => 'Informasi (Biru)',
                                'warning' => 'Peringatan (Kuning / Amber)',
                                'success' => 'Kabar Baik / Lulus (Hijau)',
                                'danger' => 'Penting / Mendesak (Merah)',
                            ])
                            ->required()
                            ->native(false),
                        TextInput::make('announcement_title')
                            ->label('Judul Pengumuman')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),
                        Textarea::make('announcement_body')
                            ->label('Isi Pengumuman')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Kotak Informasi Penting')
                    ->description('Daftar poin panduan dan informasi yang tampil pada kartu Informasi Penting di Dashboard Pendaftar.')
                    ->schema([
                        TextInput::make('info_heading')
                            ->label('Judul Kartu Informasi')
                            ->required()
                            ->maxLength(100),
                        Toggle::make('show_info_when_closed')
                            ->label('Tetap tampilkan Informasi Penting & Kontak Panitia meskipun pendaftaran sudah ditutup')
                            ->default(true),
                        Repeater::make('info_items')
                            ->label('Daftar Poin Informasi')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Poin')
                                    ->required()
                                    ->maxLength(120),
                                Textarea::make('description')
                                    ->label('Penjelasan (Opsional jika hanya tautan)')
                                    ->rows(2)
                                    ->nullable()
                                    ->helperText('Jika memasukkan link di sini atau pada isian link di bawah, tampilan akan otomatis diubah menjadi tombol yang rapi.'),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('link_url')
                                            ->label('Tautan / URL Tombol (Opsional)')
                                            ->placeholder('https://chat.whatsapp.com/... atau https://...')
                                            ->url()
                                            ->helperText('Jika diisi, otomatis muncul tombol link di bawah poin.'),
                                        TextInput::make('link_label')
                                            ->label('Label Tombol (Opsional)')
                                            ->placeholder('Contoh: Gabung Group WA / Buka Link')
                                            ->helperText('Kosongkan untuk otomatis ("Gabung Grup WhatsApp" / "Buka Tautan")'),
                                    ]),
                            ])
                            ->defaultItems(4)
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ]),

                Section::make('Kontak Panitia di Dashboard')
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact_heading')
                            ->label('Judul Bagian Kontak')
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),
                        TextInput::make('contact_phone')
                            ->label('Nomor Telepon')
                            ->maxLength(50),
                        TextInput::make('contact_email')
                            ->label('Email Panitia')
                            ->email()
                            ->maxLength(100),
                        TextInput::make('contact_whatsapp')
                            ->label('Nomor / Info WhatsApp')
                            ->maxLength(80)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        AppSetting::setPendaftarDashboardContent($state);

        Notification::make()
            ->title('Pengumuman dashboard pendaftar berhasil disimpan')
            ->success()
            ->send();
    }
}
