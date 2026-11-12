<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $content['site_subtitle'] }} - {{ $content['school_name'] }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden">
    @php
        $isPendaftarLoggedIn = auth()->check() && auth()->user()->isPendaftar();
        $pendaftarDashboardUrl = route('filament.pendaftar.pages.dashboard');
        $pendaftarLoginUrl = route('filament.pendaftar.auth.login');
        $pendaftarRegisterUrl = route('filament.pendaftar.auth.register');
        $primaryHref = $isPendaftarLoggedIn
            ? $pendaftarDashboardUrl
            : ($isRegistrationClosed ? $pendaftarLoginUrl : $pendaftarRegisterUrl);
        $primaryLabel = $isPendaftarLoggedIn
            ? 'Buka Dashboard Pendaftar'
            : ($isRegistrationClosed ? $content['secondary_button_text'] : $content['primary_button_text']);
    @endphp

    {{-- Optional Top Announcement Banner --}}
    @if (!empty($content['announcement_enabled']) && !empty($content['announcement_text']))
        <div class="bg-amber-50 border-b border-amber-200 text-amber-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 text-xs sm:text-sm flex items-start sm:items-center justify-start sm:justify-center gap-2 text-left sm:text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] sm:text-xs font-semibold bg-amber-200/80 text-amber-950 shrink-0 mt-0.5 sm:mt-0">
                    Info
                </span>
                <span class="leading-relaxed">{{ $content['announcement_text'] }}</span>
            </div>
        </div>
    @endif

    {{-- Navbar --}}
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo {{ $content['school_name'] }}" class="h-9 w-9 sm:h-11 sm:w-11 object-contain shrink-0">
                <div class="min-w-0">
                    <div class="text-xs sm:text-base font-bold text-slate-900 truncate">
                        {{ $content['school_name'] }}
                    </div>
                    <div class="text-[11px] sm:text-xs text-blue-700 font-medium truncate">
                        {{ $content['site_subtitle'] }} • {{ $content['academic_year'] }}
                    </div>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                <a href="#alur" class="hover:text-blue-700 transition">Alur Pendaftaran</a>
                <a href="#persyaratan" class="hover:text-blue-700 transition">Persyaratan</a>
                <a href="#kontak" class="hover:text-blue-700 transition">Kontak</a>
            </nav>

            <div class="flex items-center gap-2 shrink-0">
                @if ($isPendaftarLoggedIn)
                    <a href="{{ $pendaftarDashboardUrl }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs sm:text-sm font-semibold shadow-sm whitespace-nowrap transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ $pendaftarLoginUrl }}"
                       class="hidden sm:inline-flex items-center px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold whitespace-nowrap transition">
                        {{ $content['secondary_button_text'] }}
                    </a>
                    @php
                        $compactMobileLabel = $isRegistrationClosed ? 'Masuk' : 'Daftar';
                    @endphp
                    <a href="{{ $primaryHref }}"
                       class="inline-flex items-center gap-1 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs sm:text-sm font-semibold shadow-sm whitespace-nowrap transition">
                        <span class="sm:hidden">{{ $compactMobileLabel }}</span>
                        <span class="hidden sm:inline">{{ $primaryLabel }}</span>
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                @endif

                <button type="button"
                        id="mobile-menu-toggle"
                        aria-controls="mobile-menu"
                        aria-expanded="false"
                        aria-label="Buka menu navigasi"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                    <svg id="mobile-menu-icon-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="mobile-menu-icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Dropdown Menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white">
            <div class="px-4 py-3 space-y-1 text-sm font-medium text-slate-700">
                <a href="#alur" class="mobile-nav-link block px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-blue-700 transition">Alur Pendaftaran</a>
                <a href="#persyaratan" class="mobile-nav-link block px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-blue-700 transition">Persyaratan</a>
                <a href="#kontak" class="mobile-nav-link block px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-blue-700 transition">Kontak</a>

                <div class="pt-2 mt-2 border-t border-slate-100 flex flex-col gap-2">
                    @if ($isPendaftarLoggedIn)
                        <a href="{{ $pendaftarDashboardUrl }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-700 text-white font-semibold text-sm">
                            Buka Dashboard Pendaftar
                        </a>
                    @else
                        <a href="{{ $primaryHref }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-700 text-white font-semibold text-sm">
                            {{ $primaryLabel }}
                        </a>
                        @if (! $isRegistrationClosed)
                            <a href="{{ $pendaftarLoginUrl }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-semibold text-sm">
                                {{ $content['secondary_button_text'] }}
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-950 via-blue-800 to-sky-700 text-white">
        <div class="absolute inset-0 opacity-15 pointer-events-none"
             style="background-image: radial-gradient(circle at 20% 20%, #ffffff 1px, transparent 1px), radial-gradient(circle at 80% 70%, #ffffff 1px, transparent 1px); background-size: 36px 36px;">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-7 space-y-5 sm:space-y-6">
                    <div class="flex flex-wrap items-center gap-2">
                        @if (!empty($content['hero_badge']))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/15 text-blue-50 border border-white/20">
                                {{ $content['hero_badge'] }}
                            </span>
                        @endif

                        @if ($isRegistrationClosed)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-100 border border-red-300/30">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                Pendaftaran Ditutup
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-100 border border-emerald-300/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Pendaftaran Dibuka
                                @if ($closedDateFormatted)
                                    • s.d. {{ $closedDateFormatted }}
                                @endif
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                        {{ $content['hero_title'] }}
                    </h1>

                    <p class="text-sm sm:text-lg text-blue-100 leading-relaxed max-w-2xl">
                        {{ $content['hero_description'] }}
                    </p>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
                        <a href="{{ $primaryHref }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white text-blue-900 hover:bg-blue-50 font-bold text-sm sm:text-base shadow-lg shadow-blue-950/25 transition">
                            <span>{{ $primaryLabel }}</span>
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>

                        @if (! $isPendaftarLoggedIn && ! $isRegistrationClosed)
                            <a href="{{ $pendaftarLoginUrl }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-900/50 hover:bg-blue-900/70 text-white border border-white/25 font-semibold text-sm sm:text-base transition">
                                {{ $content['secondary_button_text'] }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-5">
                    @if (!empty($content['hero_image']))
                        <div class="rounded-2xl overflow-hidden bg-white/10 border border-white/20 shadow-2xl p-2">
                            <img src="{{ asset('storage/' . $content['hero_image']) }}"
                                 alt="{{ $content['school_name'] }}"
                                 class="w-full h-56 sm:h-72 lg:h-80 object-cover rounded-xl">
                        </div>
                    @else
                        <div class="rounded-2xl bg-white/10 backdrop-blur border border-white/20 shadow-2xl p-5 sm:p-8 space-y-5 sm:space-y-6">
                            <div class="flex items-center gap-3.5 sm:gap-4">
                                <div class="p-2.5 sm:p-3 rounded-2xl bg-white shadow-md shrink-0">
                                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-12 sm:h-14 sm:w-14 object-contain">
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] sm:text-xs uppercase tracking-wider text-blue-200 font-semibold">Portal Resmi</p>
                                    <h2 class="text-base sm:text-lg font-bold text-white leading-snug">{{ $content['school_name'] }}</h2>
                                    <p class="text-xs sm:text-sm text-blue-100">{{ $content['academic_year'] }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                <div class="rounded-xl bg-white/10 border border-white/15 p-3.5">
                                    <div class="text-blue-200 text-xs">Status Portal</div>
                                    <div class="font-bold text-white mt-0.5">
                                        {{ $isRegistrationClosed ? 'Ditutup' : 'Aktif / Dibuka' }}
                                    </div>
                                </div>
                                <div class="rounded-xl bg-white/10 border border-white/15 p-3.5">
                                    <div class="text-blue-200 text-xs">Proses Seleksi</div>
                                    <div class="font-bold text-white mt-0.5">Online Terintegrasi</div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-white/15 flex flex-wrap items-center justify-between gap-2 text-xs sm:text-sm text-blue-100">
                                <span>Sudah punya akun pendaftar?</span>
                                <a href="{{ $pendaftarLoginUrl }}" class="font-bold text-white underline underline-offset-4 hover:text-blue-200">
                                    Login di sini →
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Alur & Tahapan Pendaftaran --}}
    <section id="alur" class="py-10 sm:py-16 lg:py-20 bg-white border-b border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center mb-8 sm:mb-12">
                <h2 class="text-xl sm:text-3xl font-extrabold text-slate-900">
                    {{ $content['timeline_title'] }}
                </h2>
                @if (!empty($content['timeline_subtitle']))
                    <p class="mt-2.5 sm:mt-3 text-slate-600 text-sm sm:text-base">
                        {{ $content['timeline_subtitle'] }}
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach ($content['timeline_items'] as $item)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 sm:p-6 flex flex-col justify-between hover:border-blue-300 hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3.5 sm:mb-4">
                                <span class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-700 text-white font-bold text-sm">
                                    {{ $item['step'] ?? $loop->iteration }}
                                </span>
                                @if (!empty($item['date_label']))
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-blue-800">
                                        {{ $item['date_label'] }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-base font-bold text-slate-900 mb-1.5 sm:mb-2">
                                {{ $item['title'] ?? '' }}
                            </h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                {{ $item['description'] ?? '' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Persyaratan & Portal CTA --}}
    <section id="persyaratan" class="py-10 sm:py-16 lg:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
                <div class="lg:col-span-7 space-y-5 sm:space-y-6">
                    <div>
                        <h2 class="text-xl sm:text-3xl font-extrabold text-slate-900">
                            {{ $content['info_title'] }}
                        </h2>
                        @if (!empty($content['info_description']))
                            <p class="mt-2 text-slate-600 text-sm sm:text-base">
                                {{ $content['info_description'] }}
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($content['requirements'] as $req)
                            <div class="rounded-2xl bg-white border border-slate-200 p-4 sm:p-5 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                                            {{ $req['title'] ?? '' }}
                                        </h3>
                                        <p class="mt-1 text-sm text-slate-600 leading-relaxed">
                                            {{ $req['detail'] ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white p-6 sm:p-8 shadow-xl space-y-4 sm:space-y-5">
                        <h3 class="text-lg sm:text-xl font-bold">
                            Siap Mengakses Portal Pendaftar?
                        </h3>
                        <p class="text-sm text-blue-100 leading-relaxed">
                            @if ($isRegistrationClosed)
                                Pendaftaran baru saat ini sudah ditutup. Peserta yang telah memiliki akun dapat masuk ke portal untuk melihat status verifikasi dan pengumuman seleksi.
                            @else
                                Silakan daftar akun baru jika belum pernah mendaftar, atau masuk ke portal untuk melanjutkan pengisian formulir pendaftaran Anda.
                            @endif
                        </p>

                        <div class="space-y-3 pt-1">
                            <a href="{{ $primaryHref }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white text-blue-900 hover:bg-blue-50 font-bold text-sm transition">
                                <span>{{ $primaryLabel }}</span>
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>

                            @if (! $isPendaftarLoggedIn && ! $isRegistrationClosed)
                                <a href="{{ $pendaftarLoginUrl }}"
                                   class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl bg-blue-800/70 hover:bg-blue-800 border border-white/25 text-white font-semibold text-sm transition">
                                    {{ $content['secondary_button_text'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Kontak & Footer --}}
    <footer id="kontak" class="bg-slate-900 text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                <div class="md:col-span-6 space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-10 object-contain bg-white rounded-lg p-1 shrink-0">
                        <div class="min-w-0">
                            <div class="font-bold text-white">{{ $content['school_name'] }}</div>
                            <div class="text-xs text-slate-400">{{ $content['site_subtitle'] }}</div>
                        </div>
                    </div>
                    @if (!empty($content['contact_address']))
                        <p class="text-sm text-slate-400 max-w-md leading-relaxed">
                            {{ $content['contact_address'] }}
                        </p>
                    @endif
                </div>

                <div class="md:col-span-6 space-y-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white">
                        {{ $content['contact_title'] }}
                    </h3>
                    <div class="space-y-2 text-sm break-words">
                        @if (!empty($content['contact_phone']))
                            <div>Telepon: <span class="text-white font-medium">{{ $content['contact_phone'] }}</span></div>
                        @endif
                        @if (!empty($content['contact_email']))
                            <div>Email: <a href="mailto:{{ $content['contact_email'] }}" class="text-sky-400 hover:underline break-all">{{ $content['contact_email'] }}</a></div>
                        @endif
                        @if (!empty($content['contact_whatsapp']))
                            <div class="pt-1">
                                <a href="{{ $content['contact_whatsapp'] }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                                    Hubungi / Info WhatsApp
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-8 sm:mt-10 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 text-center sm:text-left">
                <span>{{ $content['footer_text'] }}</span>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ $pendaftarLoginUrl }}" class="hover:text-slate-300 transition">Portal Pendaftar</a>
                    <a href="{{ url('/admin/login') }}" class="hover:text-slate-300 transition">Admin</a>
                    <a href="{{ url('/verifikator/login') }}" class="hover:text-slate-300 transition">Verifikator</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            var btn = document.getElementById('mobile-menu-toggle');
            var menu = document.getElementById('mobile-menu');
            var iconOpen = document.getElementById('mobile-menu-icon-open');
            var iconClose = document.getElementById('mobile-menu-icon-close');
            if (!btn || !menu) return;

            function setOpen(open) {
                menu.classList.toggle('hidden', !open);
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (iconOpen) iconOpen.classList.toggle('hidden', open);
                if (iconClose) iconClose.classList.toggle('hidden', !open);
            }

            btn.addEventListener('click', function () {
                setOpen(menu.classList.contains('hidden'));
            });

            menu.querySelectorAll('.mobile-nav-link').forEach(function (link) {
                link.addEventListener('click', function () {
                    setOpen(false);
                });
            });
        })();
    </script>
</body>
</html>
