{{-- resources/views/filament/pendaftar/widgets/status-pendaftaran-widget.blade.php --}}

<x-filament::widget>
    @php
        $pendaftaran = \App\Models\Pendaftaran::where('user_id', auth()->id())->first();
        $user = auth()->user();
        
        // ✅ GANTI DENGAN LINK WHATSAPP GROUP ANDA
        $whatsappGroupLink = 'https://chat.whatsapp.com/EpFXTPqpA12D7C7NOxVhSK';
        $whatsappGroupLink2 = 'https://chat.whatsapp.com/Ijw01R93Tya6927QCHazAT';
    @endphp

    <div class="space-y-4">
        {{-- Header Section --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2 text-gray-900">
                        👋 Selamat Datang, {{ $user->name }}!
                    </h2>
                    <p class="text-gray-600 text-sm">
                        Sistem Penerimaan Murid Baru (SPMB) 2026/2027
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-gray-500 text-xs mb-1">Tanggal Hari Ini</div>
                    <div class="text-lg font-semibold text-gray-900">{{ now()->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        {{-- Alert Registration Closed --}}
        @if($this->isRegistrationClosed())
        <div class="relative overflow-hidden bg-red-400 rounded-2xl p-6 shadow-lg">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mt-20 -mr-20"></div>

            <div class="relative flex items-start gap-4">
                <div class="flex-shrink-0 p-2 bg-white/20 backdrop-blur-sm rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2">
                        Pendaftaran Telah Ditutup
                    </h3>
                    <p class="text-sm text-red-50">Mohon maaf pendaftaran SPMB SMAN Taruna Nala Jawa Timur saat ini sudah ditutup. Silahkan menunggu hasil seleksi tahap 1.</p>
                    <p class="text-sm text-red-50">Panitia SPMB Tahun Pelajaran 2026/2027</p>

                </div>
            </div>
        </div>
        @endif       

        {{-- Section Status Penerimaan --}}
        @if($pendaftaran->penerimaan)
            @if($pendaftaran->penerimaan->status === 'lolos')
                <div class="relative overflow-hidden bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6 shadow-lg">
                    <div class=" flex gap-3 mb-4 items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-green-800">
                                Status Penerimaan: LOLOS
                            </h3>
                            <p class="text-sm text-green-700">
                                Nomor: <span class="font-semibold">{{ $pendaftaran->nomor_pendaftaran }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Info Seleksi Offline untuk yang diterima --}}
                    <div class="rounded-xl mb-4">

                        <div class="flex items-center gap-3 mb-4">
                            
                            <div>

                                <p class="text-sm text-green-900 mb-3 space-y-1.5">
                                    Selamat <strong>{{ $pendaftaran->dataPribadi->nama_lengkap }}</strong> asal sekolah <strong>{{ $pendaftaran->dataSekolahAsal->nama_sekolah }}</strong>, Ananda dinyatakan Lolos Seleksi Tahap 1. <br>
                                    Mohon melakukan pembayaran biaya seleksi tahap 2 melalui Virtual Account bank jatim Ananda dengan nomor <strong>{{ $pendaftaran->penerimaan->nomor_va }}</strong>. <br>
                                    Jangan lupa mempersiapkan diri untuk Seleksi Tahap 2 di SMA Negeri Taruna Nala Jawa Timur pada tanggal 5-10 Januari 2026. <br>
                                    Anda wajib hadir di SMAN Taruna Nala Jawa Timur pada <strong>{{ $pendaftaran->penerimaan->sesi }}</strong>, silahkan membaca panduan pada tautan berikut. <br>
                                    Pengumuman ini dapat digunakan sebagai dasar mengajukan izin ke Sekolah asal (Panitia PPDB SMANTARNALA Tahun 2026)<br>
                                </p>
                            </div>
                        </div>

                        {{-- Download Buttons --}}
                        <div class="grid grid-cols-1">
                            <a href="#"
                                class="flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Panduan Seleksi
                            </a>
                            
                        </div>
                    </div>
                    
                </div>
            @else
                <div class="relative overflow-hidden bg-white border border-red-200 rounded-2xl p-6 shadow-lg">
                    <div class=" flex gap-3 mb-4 items-center">
                        <div class="p-2 bg-red-300 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Status Penerimaan: TIDAK LOLOS
                            </h3>
                            <p class="text-sm text-gray-700">
                                Nomor: <span class="font-semibold">{{ $pendaftaran->nomor_pendaftaran }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Info Seleksi Offline untuk yang tidak diterima --}}
                    <div class="rounded-xl">

                        <div class="flex items-center gap-3">
                            
                            <div>

                                <p class="text-sm text-gray-900 mb-3">
                                    Terima kasih <strong>{{ $pendaftaran->dataPribadi->nama_lengkap }}</strong> atas partisipasi Ananda dalam proses seleksi tahap 1 di SMA Negeri Taruna Nala Jawa Timur. <br>
                                    Setelah melalui proses seleksi yang ketat, dengan berat hati kami sampaikan bahwa Ananda belum berhasil lolos ke tahap berikutnya. <br>
                                    Kami mengapresiasi usaha dan dedikasi Ananda selama proses seleksi ini. Jangan berkecil hati, teruslah berusaha dan tingkatkan kemampuan untuk kesempatan di masa depan. <br>
                                    Semoga sukses selalu menyertai langkah Ananda ke depan. Terima kasih telah memilih SMA Negeri Taruna Nala Jawa Timur sebagai salah satu pilihan pendidikan Ananda. (Panitia PPDB SMANTARNALA Tahun 2026)<br>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        @if($pendaftaran)
            {{-- Status Pendaftaran --}}
            <div id="status-pendaftaran" class="bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-200 dark:border-gray-700 p-6 shadow-lg scroll-mt-4">
                
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            Status Pendaftaran Anda
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Nomor: <span class="font-semibold">{{ $pendaftaran->nomor_pendaftaran }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <div class="flex-1">
                        @php
                            $statusConfig = [
                                'draft' => [
                                    'color' => 'gray',
                                    'icon' => '📝',
                                    'label' => 'Draft',
                                    'desc' => 'Sedang dalam proses pengisian'
                                ],
                                'submitted' => [
                                    'color' => 'yellow',
                                    'icon' => '⏳',
                                    'label' => 'Menunggu Verifikasi',
                                    'desc' => 'Sedang dalam antrian verifikasi'
                                ],
                                'needs_revision' => [
                                    'color' => 'red',
                                    'icon' => '⚠️',
                                    'label' => 'Perlu Perbaikan',
                                    'desc' => 'Mohon perbaiki data sesuai catatan'
                                ],
                                'approved' => [
                                    'color' => 'green',
                                    'icon' => '✅',
                                    'label' => 'Disetujui',
                                    'desc' => 'Pendaftaran Anda telah disetujui'
                                ],
                                'rejected' => [
                                    'color' => 'gray',
                                    'icon' => '❌',
                                    'label' => 'Ditolak',
                                    'desc' => 'Pendaftaran tidak memenuhi syarat'
                                ],
                            ];
                            
                            $config = $statusConfig[$pendaftaran->status] ?? $statusConfig['draft'];
                        @endphp

                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ $config['icon'] }}</span>
                            <div>
                                <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
                                    {{ $config['label'] }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $config['desc'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        @if($pendaftaran->submitted_at)
                            <div class="text-xs text-gray-500 dark:text-gray-400">Disubmit pada:</div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $pendaftaran->submitted_at->format('d M Y, H:i') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Progress Bar --}}
                @php
                    $progress = 0;
                    $completedSections = 0;
                    $totalSections = 5;
                    
                    if ($pendaftaran->dataPribadi && $pendaftaran->dataPribadi->nama_lengkap) {
                        $completedSections++;
                    }
                    if ($pendaftaran->dataOrangTua && $pendaftaran->dataOrangTua->nama_ayah) {
                        $completedSections++;
                    }
                    if ($pendaftaran->dataSekolahAsal && $pendaftaran->dataSekolahAsal->nama_sekolah) {
                        $completedSections++;
                    }
                    if ($pendaftaran->nilaiRaport && $pendaftaran->nilaiRaport->count() >= 4) {
                        $completedSections++;
                    }
                    if ($pendaftaran->prestasi_organisasi_completed) {
                        $completedSections++;
                    }
                    
                    $progress = ($completedSections / $totalSections) * 100;
                @endphp

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Kelengkapan Data
                        </span>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                            {{ $completedSections }}/{{ $totalSections }} Bagian
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" 
                             style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-right">
                        {{ number_format($progress, 0) }}% Selesai
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-4 flex gap-2">
                    @if($pendaftaran->status === 'draft' || $pendaftaran->status === 'needs_revision')
                        <a href="{{ \App\Filament\Pendaftar\Resources\PendaftaranResource::getUrl('edit-data-pribadi', ['record' => $pendaftaran]) }}" 
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ $pendaftaran->status === 'needs_revision' ? 'Perbaiki Data' : 'Lengkapi Data' }}
                        </a>
                    @endif

                    @if($pendaftaran->status === 'submitted' || $pendaftaran->status === 'approved')
                        <a href="{{ \App\Filament\Pendaftar\Resources\PendaftaranResource::getUrl('cetak-bukti', ['record' => $pendaftaran]) }}" 
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Bukti Pendaftaran
                        </a>
                    @endif

                    <a href="{{ \App\Filament\Pendaftar\Resources\PendaftaranResource::getUrl('index') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Lihat Detail
                    </a>
                </div>

            </div>

        @else
            {{-- Belum Ada Pendaftaran --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                    Belum Ada Pendaftaran
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                    Anda belum memiliki pendaftaran aktif. Klik tombol di bawah untuk memulai proses pendaftaran SPMB 2025/2026.
                </p>
                <a href="{{ \App\Filament\Pendaftar\Resources\PendaftaranResource::getUrl('create') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Mulai Pendaftaran
                </a>
            </div>
        @endif

        {{-- Tombol Join WhatsApp Group --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mt-20 -mr-20"></div>

            <div class="relative">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg">Gabung Grup WhatsApp SPMB</h3>
                        <p class="text-green-50 text-sm">Dapatkan info terbaru & tanya jawab langsung</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <a href="{{ $whatsappGroupLink }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold rounded-xl transition-all duration-300 shadow-lg opacity-75 cursor-not-allowed">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <div class="flex flex-col items-start">
                                <span>Join Grup 1 (sudah penuh)</span>
                            </div>
                        </a>
                    </div>

                    <div class="flex-1 relative">
                        <a href="{{ $whatsappGroupLink2 }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-white hover:bg-green-50 text-green-600 font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <div class="flex flex-col items-start">
                                <span>Join Grup 2</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::widget>