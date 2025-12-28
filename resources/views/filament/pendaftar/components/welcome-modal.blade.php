@php
    $pendaftaran = \App\Models\Pendaftaran::where('user_id', auth()->id())->first();
    $user = auth()->user();
@endphp

<div
    x-data="{
        open: false,
        dontShowAgain: false,
        init() {
            // Cek localStorage apakah user sudah memilih untuk tidak menampilkan lagi hari ini
            const hideUntil = localStorage.getItem('hideWelcomeModal');
            const today = new Date().toDateString();

            if (!hideUntil || hideUntil !== today) {
                this.open = true;
            }
        },
        closeModal() {
            if (this.dontShowAgain) {
                // Simpan tanggal hari ini ke localStorage
                const today = new Date().toDateString();
                localStorage.setItem('hideWelcomeModal', today);
            }
            this.open = false;
        }
    }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    {{-- Overlay --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="closeModal()"
    ></div>

    {{-- Modal Content --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl max-h-[90vh] overflow-y-auto"
        >
            {{-- Header --}}
            <div class="bg-primary-600 px-6 py-4 sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white">
                            <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white">
                            Selamat Datang, {{ $user->name }}!
                        </h3>
                    </div>
                    <button
                        @click="closeModal()"
                        type="button"
                        class="text-white hover:text-gray-200 transition-colors"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="bg-white px-6 py-5">
                <div class="space-y-4">
                    

                    @if($pendaftaran)
                        {{-- STATUS PENERIMAAN --}}
                        @if($pendaftaran->penerimaan)
                            @if($pendaftaran->penerimaan->status === 'lolos')
                                <div class="rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 p-4 shadow-sm">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="p-2 bg-green-100 rounded-lg">
                                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-green-900 text-base mb-1">
                                                Status Penerimaan: LOLOS TAHAP 1
                                            </h4>
                                            <p class="text-sm text-green-700">
                                                Nomor: <span class="font-semibold">{{ $pendaftaran->nomor_pendaftaran }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-sm text-green-900 space-y-2">
                                        <p>Selamat <strong>{{ $pendaftaran->dataPribadi->nama_lengkap }}</strong>, Anda dinyatakan Lolos Seleksi Tahap 1.</p>
                                        <ul class="list-disc list-inside space-y-1 ml-2">
                                            <li>Lakukan pembayaran biaya seleksi tahap 2 melalui VA: <strong>{{ $pendaftaran->penerimaan->nomor_va }}</strong></li>
                                            <li>Seleksi Tahap 2: <strong>5-10 Januari 2026</strong></li>
                                            <li><strong>{{ $pendaftaran->penerimaan->sesi }}</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-lg bg-red-50 border-2 border-red-300 p-4 shadow-sm">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-red-200 rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 text-base mb-1">
                                                Status Penerimaan: TIDAK LOLOS
                                            </h4>
                                            <p class="text-sm text-gray-700 mt-2">
                                                Terima kasih atas partisipasi Anda. Jangan berkecil hati, teruslah berusaha untuk kesempatan di masa depan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- STATUS PENDAFTARAN --}}
                            @php
                                $statusConfig = [
                                    'draft' => [
                                        'color' => 'blue',
                                        'icon' => '📝',
                                        'label' => 'Draft - Sedang Proses Pengisian',
                                        'desc' => 'Silakan lengkapi semua data pendaftaran Anda.'
                                    ],
                                    'submitted' => [
                                        'color' => 'yellow',
                                        'icon' => '⏳',
                                        'label' => 'Menunggu Verifikasi',
                                        'desc' => 'Data Anda sedang dalam proses verifikasi oleh panitia.'
                                    ],
                                    'needs_revision' => [
                                        'color' => 'red',
                                        'icon' => '⚠️',
                                        'label' => 'Perlu Perbaikan',
                                        'desc' => 'Silakan perbaiki data sesuai catatan dari panitia.'
                                    ],
                                    'approved' => [
                                        'color' => 'green',
                                        'icon' => '✅',
                                        'label' => 'Disetujui',
                                        'desc' => 'Pendaftaran Anda telah disetujui. Tunggu pengumuman seleksi.'
                                    ],
                                    'rejected' => [
                                        'color' => 'gray',
                                        'icon' => '❌',
                                        'label' => 'Ditolak',
                                        'desc' => 'Pendaftaran tidak memenuhi syarat yang ditentukan.'
                                    ],
                                ];

                                $config = $statusConfig[$pendaftaran->status] ?? $statusConfig['draft'];
                            @endphp

                            <div class="rounded-lg bg-{{ $config['color'] }}-50 border-2 border-{{ $config['color'] }}-200 p-4 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="text-3xl">{{ $config['icon'] }}</div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-{{ $config['color'] }}-900 text-base mb-1">
                                            {{ $config['label'] }}
                                        </h4>
                                        <p class="text-sm text-{{ $config['color'] }}-800">
                                            Nomor: <span class="font-semibold">{{ $pendaftaran->nomor_pendaftaran }}</span>
                                        </p>
                                        <p class="text-sm text-{{ $config['color'] }}-700 mt-2">
                                            {{ $config['desc'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- WARNING CALO & PENIPUAN --}}
                    <div class="rounded-lg bg-yellow-50 p-4 border-2 border-yellow-100 shadow-sm">
                        <div class="flex items-center gap-3">
                            
                            <div>
                                <h4 class="font-bold text-orange-700 text-base">
                                    WASPADA TERHADAP CALO & PENIPUAN 
                                        SELEKSI SPMB SMAN TARUNA NALA JAWA TIMUR TAHUN AJARAN 2026/2027
                                </h4>
                                <p class="text-sm text-orange-700">
                                    Pengumuman resmi hanya melalui <span class="font-bold">akun SPMB masing-masing pendaftar</span> dan <span class="font-bold">humas sekolah</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 sticky bottom-0">
                <div class="space-y-3">
                    {{-- Checkbox Jangan Tampilkan Lagi --}}
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            x-model="dontShowAgain"
                            class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                        >
                        <span class="text-sm text-gray-700">Jangan tampilkan lagi hari ini</span>
                    </label>

                    {{-- Button Saya Mengerti --}}
                    <button
                        @click="closeModal()"
                        type="button"
                        class="w-full rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                    >
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
