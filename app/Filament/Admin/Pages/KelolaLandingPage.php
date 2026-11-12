<?php

namespace App\Filament\Admin\Pages;

use App\Models\AppSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class KelolaLandingPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Kelola Landing Page';

    protected static ?string $title = 'Kelola Konten Landing Page';

    protected static ?int $navigationSort = 98;

    protected static string $view = 'filament.admin.pages.kelola-landing-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(AppSetting::getLandingContent());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Lihat Landing Page')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(url('/'), shouldOpenInNewTab: true),

            Action::make('resetDefault')
                ->label('Reset ke Default')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reset Konten Landing Page?')
                ->modalDescription('Semua teks, alur tahapan, persyaratan, dan kontak pada landing page akan dikembalikan ke isi default.')
                ->action(function (): void {
                    AppSetting::resetLandingContent();
                    $this->form->fill(AppSetting::getLandingContent());

                    Notification::make()
                        ->title('Konten landing page dikembalikan ke default')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('KontenLandingPage')
                    ->tabs([
                        Tabs\Tab::make('Identitas & Hero')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make('Identitas Sekolah')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('school_name')
                                            ->label('Nama Sekolah')
                                            ->required()
                                            ->maxLength(120),
                                        TextInput::make('site_subtitle')
                                            ->label('Subjudul Sistem')
                                            ->required()
                                            ->maxLength(120),
                                        TextInput::make('academic_year')
                                            ->label('Tahun Ajaran')
                                            ->required()
                                            ->maxLength(60),
                                    ]),

                                Section::make('Bagian Utama (Hero) & Tombol Pendaftar')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('hero_badge')
                                            ->label('Teks Badge Atas')
                                            ->maxLength(80),
                                        TextInput::make('primary_button_text')
                                            ->label('Teks Tombol Utama (ke Daftar / Portal)')
                                            ->required()
                                            ->maxLength(50),
                                        TextInput::make('hero_title')
                                            ->label('Judul Utama Landing Page')
                                            ->required()
                                            ->maxLength(180)
                                            ->columnSpanFull(),
                                        Textarea::make('hero_description')
                                            ->label('Deskripsi Singkat Hero')
                                            ->rows(3)
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('secondary_button_text')
                                            ->label('Teks Tombol Login Pendaftar')
                                            ->required()
                                            ->maxLength(50),
                                        FileUpload::make('hero_image')
                                            ->label('Gambar / Foto Hero (Opsional)')
                                            ->helperText('Jika dikosongkan, halaman depan akan menampilkan kartu info SPMB & logo sekolah.')
                                            ->image()
                                            ->disk('public')
                                            ->directory('landing')
                                            ->maxSize(2048),
                                    ]),
                            ]),

                        Tabs\Tab::make('Alur Pendaftaran')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                Section::make('Judul Bagian Alur')
                                    ->schema([
                                        TextInput::make('timeline_title')
                                            ->label('Judul Bagian')
                                            ->required()
                                            ->maxLength(120),
                                        Textarea::make('timeline_subtitle')
                                            ->label('Subjudul Bagian')
                                            ->rows(2),
                                    ]),

                                Repeater::make('timeline_items')
                                    ->label('Daftar Tahapan Pendaftaran')
                                    ->schema([
                                        TextInput::make('step')
                                            ->label('Nomor')
                                            ->placeholder('01')
                                            ->required()
                                            ->maxLength(10),
                                        TextInput::make('date_label')
                                            ->label('Label / Waktu')
                                            ->placeholder('Tahap Awal')
                                            ->maxLength(60),
                                        TextInput::make('title')
                                            ->label('Judul Tahap')
                                            ->required()
                                            ->maxLength(100),
                                        Textarea::make('description')
                                            ->label('Keterangan')
                                            ->rows(2)
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3)
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => trim(($state['step'] ?? '') . ' - ' . ($state['title'] ?? 'Tahapan'))),
                            ]),

                        Tabs\Tab::make('Persyaratan & Pengumuman')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Section::make('Banner Pengumuman Atas')
                                    ->schema([
                                        Toggle::make('announcement_enabled')
                                            ->label('Tampilkan Banner Pengumuman di Landing Page')
                                            ->default(true),
                                        Textarea::make('announcement_text')
                                            ->label('Isi Pengumuman')
                                            ->rows(2),
                                    ]),

                                Section::make('Persyaratan & Dokumen')
                                    ->schema([
                                        TextInput::make('info_title')
                                            ->label('Judul Persyaratan')
                                            ->required()
                                            ->maxLength(120),
                                        Textarea::make('info_description')
                                            ->label('Pengantar Persyaratan')
                                            ->rows(2),
                                        Repeater::make('requirements')
                                            ->label('Daftar Persyaratan')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Judul Poin')
                                                    ->required()
                                                    ->maxLength(100),
                                                Textarea::make('detail')
                                                    ->label('Penjelasan')
                                                    ->rows(2)
                                                    ->required(),
                                            ])
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Persyaratan'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Kontak & Footer')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('Informasi Kontak Panitia')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('contact_title')
                                            ->label('Judul Bagian Kontak')
                                            ->required()
                                            ->maxLength(120)
                                            ->columnSpanFull(),
                                        Textarea::make('contact_address')
                                            ->label('Alamat Sekolah')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        TextInput::make('contact_phone')
                                            ->label('Telepon / Hotline')
                                            ->maxLength(60),
                                        TextInput::make('contact_email')
                                            ->label('Email Panitia')
                                            ->email()
                                            ->maxLength(120),
                                        TextInput::make('contact_whatsapp')
                                            ->label('Link WhatsApp / Grup Info (Opsional)')
                                            ->placeholder('https://wa.me/628...')
                                            ->url()
                                            ->maxLength(255),
                                        TextInput::make('footer_text')
                                            ->label('Teks Footer')
                                            ->maxLength(180),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        AppSetting::setLandingContent($state);

        Notification::make()
            ->title('Konten landing page berhasil disimpan')
            ->success()
            ->send();
    }
}
