<?php

namespace App\Providers\Filament;

use App\Filament\Pendaftar\Pages\Auth\Register;
use App\Filament\Pendaftar\Resources\PendaftaranResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pendaftar\Pages\Auth\Login;
use App\Http\Middleware\CheckRegistrationOpen;
use Carbon\Carbon;

class PendaftarPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('pendaftar')
            ->path('pendaftar')
            ->login(Login::class)
            ->registration(\App\Filament\Pendaftar\Pages\Auth\Register::class)
            ->passwordReset()
               
            ->brandName(view('filament.pendaftar.brand'))
            ->favicon(asset('images/logo.png'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->viteTheme('resources/css/filament/pendaftar/theme.css')
            ->discoverResources(in: app_path('Filament/Pendaftar/Resources'), for: 'App\\Filament\\Pendaftar\\Resources')
            ->discoverPages(in: app_path('Filament/Pendaftar/Pages'), for: 'App\\Filament\\Pendaftar\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Pendaftar/Widgets'), for: 'App\\Filament\\Pendaftar\\Widgets')
            
            // ✅ CUSTOM NAVIGATION DENGAN MENU DINAMIS
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups($this->getNavigationGroups());
            })
            
            // ✅ CUSTOM CSS UNTUK NAVIGATION STYLING
            ->renderHook(
                'panels::head.end',
                fn () => '
                    <style>
                        /* ===== NAVIGATION GROUP STYLING ===== */
                        .fi-sidebar-group-button {
                            font-weight: 600 !important;
                            color: #374151 !important;
                            transition: all 0.2s ease;
                            padding: 0.5rem 0.75rem !important;
                            margin: 0.25rem 0 !important;
                        }

                        /* Group yang sedang expanded/open */
                        .fi-sidebar-group-button[aria-expanded="true"] {
                            font-weight: 700 !important;
                            color: #1d4ed8 !important;
                            background-color: rgba(59, 130, 246, 0.1) !important;
                            border-radius: 0.5rem !important;
                            border-left: 4px solid #2563eb !important;
                        }

                        /* Group label text saat expanded */
                        .fi-sidebar-group-button[aria-expanded="true"] .fi-sidebar-group-label {
                            font-weight: 700 !important;
                            color: #1d4ed8 !important;
                        }

                        /* Group icon saat expanded */
                        .fi-sidebar-group-button[aria-expanded="true"] svg {
                            color: #2563eb !important;
                        }

                        /* Hover effect pada group */
                        .fi-sidebar-group-button:hover {
                            background-color: rgba(59, 130, 246, 0.08) !important;
                            border-radius: 0.5rem !important;
                        }

                        /* ===== ACTIVE MENU ITEM STYLING ===== */
                        .fi-sidebar-item-active .fi-sidebar-item-button {
                            font-weight: 700 !important;
                            background-color: rgba(59, 130, 246, 0.15) !important;
                            border-left: 4px solid #2563eb !important;
                            border-radius: 0.375rem !important;
                        }

                        .fi-sidebar-item-active .fi-sidebar-item-label {
                            font-weight: 700 !important;
                            color: #1d4ed8 !important;
                        }

                        /* Icon pada menu aktif */
                        .fi-sidebar-item-active .fi-sidebar-item-icon {
                            color: #2563eb !important;
                        }

                        /* ===== HOVER EFFECT MENU ITEM ===== */
                        .fi-sidebar-item-button:hover {
                            background-color: rgba(59, 130, 246, 0.08) !important;
                            border-radius: 0.375rem !important;
                        }

                        /* ===== BADGE STYLING ===== */
                        .fi-sidebar-item-badge {
                            font-weight: 600 !important;
                            font-size: 0.7rem !important;
                        }

                        /* ===== GENERAL MENU ITEM ===== */
                        .fi-sidebar-item-button {
                            transition: all 0.2s ease !important;
                            margin: 0.125rem 0 !important;
                        }

                        .fi-sidebar-item-label {
                            transition: all 0.2s ease !important;
                        }

                        /* ===== DARK MODE SUPPORT ===== */
                        .dark .fi-sidebar-group-button {
                            color: #d1d5db !important;
                        }

                        .dark .fi-sidebar-group-button[aria-expanded="true"] {
                            color: #93c5fd !important;
                            background-color: rgba(59, 130, 246, 0.2) !important;
                        }

                        .dark .fi-sidebar-item-active .fi-sidebar-item-button {
                            background-color: rgba(59, 130, 246, 0.25) !important;
                        }

                        .dark .fi-sidebar-item-active .fi-sidebar-item-label {
                            color: #93c5fd !important;
                        }

                        .dark .fi-sidebar-item-button:hover {
                            background-color: rgba(59, 130, 246, 0.15) !important;
                        }
                    </style>
                '
            )

            // Modal Pemberitahuan Auto-Open
            ->renderHook(
                'panels::body.start',
                function () {
                    // Selalu tampilkan modal untuk user yang authenticated
                    if (auth()->check()) {
                        if (auth()->user()->pendaftaran->penerimaan) {
                            return view('filament.pendaftar.components.welcome-modal');
                        }
                    }

                    return '';
                }
            )
            
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                // ensure pendaftar
                \App\Http\Middleware\EnsureUserIsPendaftar::class,
            ])
            ->authGuard('web');
    }
    
    /**
     * Helper: Cek apakah pendaftaran sudah ditutup
     */
    protected function isRegistrationClosed(): bool
    {
        $closedDate = config('app.register_closed_date');

        if (!$closedDate) {
            return false;
        }

        return Carbon::now()->gte(Carbon::parse($closedDate));
    }

    /**
     * ✅ Method untuk membuat navigation groups dengan menu dinamis
     */
    protected function getNavigationGroups(): array
    {
        // Ambil data pendaftaran user yang sedang login
        $pendaftaran = \App\Models\Pendaftaran::where('user_id', auth()->id())->first();

        $groups = [];

        // Cek apakah pendaftaran sudah ditutup
        $isClosed = $this->isRegistrationClosed();

        // GROUP 1: Menu Utama
        $menuItems = [
            NavigationItem::make('Dashboard')
                ->icon('heroicon-o-home')
                ->isActiveWhen(fn () => request()->routeIs('filament.pendaftar.pages.dashboard'))
                ->url(fn () => route('filament.pendaftar.pages.dashboard')),
        ];

        // Hanya tampilkan menu "Daftar Pendaftaran" jika pendaftaran masih dibuka
        if (!$isClosed) {
            $menuItems[] = NavigationItem::make('Daftar Pendaftaran')
                ->icon('heroicon-o-list-bullet')
                ->isActiveWhen(fn () => request()->routeIs('filament.pendaftar.resources.pendaftarans.index'))
                ->badge($this->getPendaftaranBadge($pendaftaran))
                ->url(fn () => PendaftaranResource::getUrl('index'));
        }

        $groups[] = NavigationGroup::make('Menu Utama')
            ->items($menuItems);

        // GROUP 2: Form Pendaftaran (hanya tampil jika pendaftaran masih dibuka dan ada data pendaftaran)
        if (!$isClosed && $pendaftaran) {
    $completionStatus = $pendaftaran->getCompletionStatus();

    // ✅ CEK APAKAH ADA REVISI YANG DIPERLUKAN
    $needsRevision = $pendaftaran->status === 'needs_revision';
    $revisionFields = [];

    if ($needsRevision && $pendaftaran->latestVerifikasi) {
        $verifikasi = $pendaftaran->latestVerifikasi;
        $revisionFields = [
            'data_pribadi' => !$verifikasi->data_pribadi_ok,
            'data_orang_tua' => !$verifikasi->data_orang_tua_ok,
            'sekolah_asal' => !$verifikasi->sekolah_asal_ok,
            'nilai_raport' => !$verifikasi->nilai_raport_ok,
            'prestasi_organisasi' => !$verifikasi->prestasi_organisasi_ok,
        ];
    }

    $groups[] = NavigationGroup::make('📝 Isi Form Pendaftaran')
        ->items([
            NavigationItem::make('1. Data Pribadi')
                ->icon($this->getMenuIcon('data_pribadi', $completionStatus, $revisionFields))
                ->isActiveWhen(fn () => request()->routeIs('filament.pendaftar.resources.pendaftarans.edit-data-pribadi'))
                ->badge($this->getMenuBadge('data_pribadi', $completionStatus, $revisionFields))
                // ->badgeColor($this->getMenuBadgeColor('data_pribadi', $completionStatus, $revisionFields))
                ->url(fn () => PendaftaranResource::getUrl('edit-data-pribadi', ['record' => $pendaftaran])),
                
            NavigationItem::make('2. Data Orang Tua')
                ->icon($this->getMenuIcon('data_orang_tua', $completionStatus, $revisionFields))
                ->isActiveWhen(fn () => request()->routeIs('filament.pendaftar.resources.pendaftarans.edit-data-orang-tua'))
                ->badge($this->getMenuBadge('data_orang_tua', $completionStatus, $revisionFields))
                // ->badgeColor($this->getMenuBadgeColor('data_orang_tua', $completionStatus, $revisionFields))
                ->url(fn () => PendaftaranResource::getUrl('edit-data-orang-tua', ['record' => $pendaftaran])),
                
            NavigationItem::make('3. Sekolah Asal')
                ->icon($this->getMenuIcon('sekolah_asal', $completionStatus, $revisionFields))
                ->isActiveWhen(fn () => request()->routeIs('filament.pendaftar.resources.pendaftarans.edit-sekolah-asal'))
                ->badge($this->getMenuBadge('sekolah_asal', $completionStatus, $revisionFields))
                // ->badgeColor($this->getMenuBadgeColor('sekolah_asal', $completionStatus, $revisionFields))
                ->url(fn () => PendaftaranResource::getUrl('edit-sekolah-asal', ['record' => $pendaftaran])),
                
            NavigationItem::make('4. Nilai Raport')
                ->icon($this->getMenuIcon('nilai_raport', $completionStatus, $revisionFields))
                ->isActiveWhen(fn () => request()->routeIs('filament.pendaftar.resources.pendaftarans.edit-nilai-raport'))
                ->badge($this->getMenuBadge('nilai_raport', $completionStatus, $revisionFields))
                // ->badgeColor($this->getMenuBadgeColor('nilai_raport', $completionStatus, $revisionFields))
                ->url(fn () => PendaftaranResource::getUrl('edit-nilai-raport', ['record' => $pendaftaran])),
                
            NavigationItem::make('5. Prestasi & Organisasi')
                ->icon($this->getMenuIcon('prestasi_organisasi', $completionStatus, $revisionFields))
                ->isActiveWhen(fn () => request()->routeIs('filament.pendaftar.resources.pendaftarans.edit-prestasi'))
                ->badge($this->getMenuBadge('prestasi_organisasi', $completionStatus, $revisionFields))
                // ->badgeColor($this->getMenuBadgeColor('prestasi_organisasi', $completionStatus, $revisionFields))
                ->url(fn () => PendaftaranResource::getUrl('edit-prestasi', ['record' => $pendaftaran])),
        ])
        ->collapsible()
        ->collapsed(false);
                
            // GROUP 3: Aksi Pendaftaran
            $groups[] = NavigationGroup::make('🚀 Aksi Pendaftaran')
                ->items([
                    // ✅ MENU SELESAIKAN PENDAFTARAN - Aktif hanya jika semua data lengkap
                    NavigationItem::make('Selesaikan Pendaftaran')
                        ->icon($pendaftaran->canSubmit() ? 'heroicon-o-paper-airplane' : 'heroicon-o-lock-closed')
                        ->isActiveWhen(fn () => request()->routeIs('filament.pendaftar.resources.pendaftarans.submit'))
                        ->badge($pendaftaran->canSubmit() ? '🟢 SIAP SUBMIT' : '🔒 ' . $pendaftaran->getCompletionPercentage() . '%')
                        ->url(fn () => $pendaftaran->canSubmit() 
                            ? PendaftaranResource::getUrl('submit', ['record' => $pendaftaran])
                            : '#')
                        ->visible(fn () => $pendaftaran->status === 'draft')
                        ->sort($pendaftaran->canSubmit() ? 1 : 99),
                        
                    // ✅ MENU CETAK BUKTI - Aktif hanya setelah submit
                    NavigationItem::make('Cetak Bukti Pendaftaran')
                        ->icon($pendaftaran->canPrintProof() ? 'heroicon-o-printer' : 'heroicon-o-lock-closed')
                        ->isActiveWhen(fn () => request()->routeIs('filament.pendaftar.resources.pendaftarans.cetak-bukti'))
                        ->badge($pendaftaran->canPrintProof() ? '🟢 TERSEDIA' : '🔒 BELUM SUBMIT')
                        ->url(fn () => $pendaftaran->canPrintProof() 
                            ? PendaftaranResource::getUrl('cetak-bukti', ['record' => $pendaftaran])
                            : '#')
                        ->sort($pendaftaran->canPrintProof() ? 2 : 100),
                ])
                ->collapsible()
                ->collapsed(false);
        }
        
        return $groups;
    }
    
    /**
     * Helper: Get badge text untuk menu Daftar Pendaftaran
     */
    protected function getPendaftaranBadge($pendaftaran): ?string
    {
        if (!$pendaftaran) {
            return null;
        }
        
        return match($pendaftaran->status) {
            'needs_revision' => '🔴 Perlu Perbaikan',
            'draft' => '⚠️ ' . $pendaftaran->getCompletionPercentage() . '% Lengkap',
            'submitted' => '🟡 Menunggu Verifikasi',
            'approved' => '✅ Diterima',
            'rejected' => '❌ Ditolak',
            default => null,
        };
    }

    /**
 * Helper: Get icon untuk menu berdasarkan status
 */
protected function getMenuIcon(string $field, array $completionStatus, array $revisionFields): string
{
    // Jika perlu revisi
    if (isset($revisionFields[$field]) && $revisionFields[$field]) {
        return 'heroicon-o-exclamation-triangle'; // Warning icon
    }
    
    // Jika lengkap
    if ($completionStatus[$field] ?? false) {
        return 'heroicon-o-check-circle'; // Check icon
    }
    
    // Default icon berdasarkan field
    return match($field) {
        'data_pribadi' => 'heroicon-o-user',
        'data_orang_tua' => 'heroicon-o-users',
        'sekolah_asal' => 'heroicon-o-academic-cap',
        'nilai_raport' => 'heroicon-o-chart-bar',
        'prestasi_organisasi' => 'heroicon-o-trophy',
        default => 'heroicon-o-document',
    };
}

/**
 * Helper: Get badge text untuk menu berdasarkan status
 */
protected function getMenuBadge(string $field, array $completionStatus, array $revisionFields): string
{
    // Jika perlu revisi - PRIORITAS TERTINGGI
    if (isset($revisionFields[$field]) && $revisionFields[$field]) {
        return '🔴 Perlu Diperbaiki';
    }
    
    // Jika lengkap
    if ($completionStatus[$field] ?? false) {
        return '✅ Lengkap';
    }
    
    // Belum lengkap
    return '⚠️ Belum Lengkap';
}

/**
 * Helper: Get badge color untuk menu berdasarkan status
 */

}