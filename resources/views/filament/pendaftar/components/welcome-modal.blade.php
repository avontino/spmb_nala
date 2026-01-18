@php
    $pendaftaran = \App\Models\Pendaftaran::with(['dataPribadi', 'dataSekolahAsal', 'penerimaan', 'penerimaanTahap2'])->where('user_id', auth()->id())->first();
    $user = auth()->user();
    $penerimaanTahap2 = $pendaftaran?->penerimaanTahap2;
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
            @if($penerimaanTahap2)
                {{-- ========== PENGUMUMAN KELULUSAN TAHAP 2 ========== --}}
                @php
                    $namaCasis = $pendaftaran->dataPribadi?->nama_lengkap ?? '-';
                    $namaSekolah = $pendaftaran->dataSekolahAsal?->nama_sekolah ?? '-';
                    $nomorVa = $penerimaanTahap2->nomor_va ?? '-';
                    $sesiRegistrasi = $penerimaanTahap2->sesi ?? '-';
                    $statusTahap2 = $penerimaanTahap2->status;
                @endphp

                @if($statusTahap2 === 'cadangan')
                    {{-- CADANGAN --}}
                    <div class="bg-orange-500 px-6 py-4 sticky top-0 z-10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white">
                                    <svg class="h-8 w-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-white">
                                    PENGUMUMAN KELULUSAN SELEKSI TAHAP 2
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

                    <div class="bg-white px-6 py-5">
                        <div class="space-y-4 text-sm text-gray-700">
                            <p>
                                Salam sehat, <strong>{{ $namaCasis }}</strong> asal sekolah <strong>{{ $namaSekolah }}</strong>, Berdasarkan seleksi yang telah dilakukan, Ananda dinyatakan masuk pada kategori <span class="font-bold text-yellow-600">CADANGAN</span> ({{ $penerimaanTahap2->sesi }}) dalam SPMB SMA Negeri Taruna Nala Jawa Timur. Ananda, masih berkesempatan melakukan registrasi ulang sebagai calon siswa apabila:
                            </p>
                            <ol class="list-decimal list-inside space-y-1 ml-2">
                                <li>Terdapat Calon Murid yang mengundurkan diri.</li>
                                <li>Terdapat Calon Murid yang tidak melakukan Registrasi sampai dengan tanggal 21 Januari 2026.</li>
                            </ol>
                            <p>
                                Penggantian oleh casis lain yang berstatus sebagai cadangan dilakukan sesuai dengan urutan cadangan. Konfirmasi penggantian hanya akan dilakukan oleh panitia melalui nomor Panitia SPMB dengan nomor WA <a href="https://wa.me/6281313085908" target="blank" class="font-weight-500 text-primary-500">081313085908</a>. Informasi lengkap tentang Registrasi Murid Baru dapat dilihat pada Panduan Registrasi Murid Baru melalui tautan dibawah ini.
                            </p>
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-red-700 font-medium">
                                    Jangan percaya kepada siapapun yang menawarkan jasa dengan mengatasnamakan Panitia SPMB SMANTARNALA. Proses SPMB hanya dilaksanakan melalui informasi resmi sekolah.
                                </p>
                            </div>
                            <p class="text-gray-600 italic text-center">
                                (Panitia SPMB SMANTARNALA Tahun 2026)
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sticky bottom-0">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 gap-3">
                                <a
                                    href="{{ route('download-hasil-akhir-seleksi-tahap-2') }}"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Unduh Panduan Registrasi Murid Baru
                                </a>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    x-model="dontShowAgain"
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                >
                                <span class="text-sm text-gray-700">Jangan tampilkan lagi hari ini</span>
                            </label>
                            <button
                                @click="closeModal()"
                                type="button"
                                class="w-full rounded-lg bg-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>

                @elseif($statusTahap2 === 'tidak_lolos')
                    {{-- TIDAK LOLOS --}}
                    <div class="bg-red-600 px-6 py-4 sticky top-0 z-10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white">
                                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-white">
                                    TIDAK LOLOS
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

                    <div class="bg-white px-6 py-5">
                        <div class="space-y-4 text-sm text-gray-700">
                            <p>
                                Terima kasih <strong>{{ $namaCasis }}</strong> atas partisipasi Ananda dalam proses SPMB di SMA Negeri Taruna Nala Jawa Timur.
                            </p>
                            <p>
                                Setelah melalui proses seleksi yang ketat, dengan berat hati kami sampaikan bahwa Ananda <span class="font-bold text-red-600">tidak lolos</span> dalam penerimaan Tahun Pelajaran 2026/2027.
                            </p>
                            <p>
                                Kami mengapresiasi usaha dan dedikasi Ananda selama proses seleksi ini. Jangan berkecil hati, teruslah berusaha dan tingkatkan kemampuan untuk kesempatan di masa depan.
                            </p>
                            <p>
                                Semoga sukses selalu menyertai langkah Ananda ke depan. Terima kasih telah memilih SMA Negeri Taruna Nala Jawa Timur sebagai salah satu pilihan pendidikan Ananda.
                            </p>
                            <p class="text-gray-600 italic text-center">
                                (Panitia SPMB SMANTARNALA Tahun 2026)
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sticky bottom-0">
                        <div class="space-y-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    x-model="dontShowAgain"
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                >
                                <span class="text-sm text-gray-700">Jangan tampilkan lagi hari ini</span>
                            </label>
                            <button
                                @click="closeModal()"
                                type="button"
                                class="w-full rounded-lg bg-gray-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>

                @elseif($statusTahap2 === 'lolos')
                    {{-- LOLOS --}}
                    <div class="bg-green-600 px-6 py-4 sticky top-0 z-10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-white">
                                    SELAMAT! ANDA LOLOS SELEKSI TAHAP 2
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

                    <div class="bg-white px-6 py-5">
                        <div class="space-y-4 text-sm text-gray-700">
                            <p>
                                Selamat <strong>{{ $namaCasis }}</strong> asal sekolah <strong>{{ $namaSekolah }}</strong>, Berdasarkan seleksi yang telah dilakukan Ananda dinyatakan <span class="font-bold text-green-600">Lolos</span> SPMB SMA Negeri Taruna Nala Jawa Timur. Mohon membaca Panduan Registrasi Murid Baru dengan seksama pada tautan dibawah ini.
                            </p>

                            {{-- Informasi Registrasi --}}
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-3">Informasi Registrasi Calon Taruna</h4>
                                <div class="space-y-1.5">
                                    <div class="flex gap-2">
                                        <span class="text-gray-600">Nama</span>
                                        <span class="font-medium">: {{ $namaCasis }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="text-gray-600">Asal Sekolah</span>
                                        <span class="font-medium">: {{ $namaSekolah }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="text-gray-600">Nomor VA Pembayaran Registrasi</span>
                                        <span class="font-medium">: {{ $nomorVa }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="text-gray-600">Sesi Registrasi</span>
                                        <span class="font-medium">: {{ $sesiRegistrasi }}</span>
                                    </div>
                                </div>
                                <p class="mt-3 text-xs text-gray-500 italic">
                                    (Pembayaran dilakukan melalui Virtual Account bank jatim)
                                </p>
                            </div>

                            <p>
                                Bagi casis yang tidak melaksanakan Registrasi hingga tanggal 21 januari 2026 Pukul 13.00 WIB, maka akan digantikan oleh casis lain yang berstatus sebagai cadangan sesuai dengan urutan cadangan. Jangan percaya kepada siapapun yang menawarkan jasa dengan mengatasnamakan Panitia SPMB SMANTARNALA. Proses SPMB hanya dilaksanakan melalui informasi resmi sekolah dengan nomor WA <a href="https://wa.me/6281313085908" target="blank" class="font-weight-500 text-primary-500">081313085908</a>.
                            </p>

                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-red-700 font-medium">
                                    Jangan percaya kepada siapapun yang menawarkan jasa dengan mengatasnamakan Panitia SPMB SMANTARNALA. Proses SPMB hanya dilaksanakan melalui informasi resmi sekolah.
                                </p>
                            </div>
                            <p class="text-gray-600 italic text-center">
                                (Panitia SPMB SMANTARNALA Tahun 2026)
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sticky bottom-0">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 gap-3">
                                <a
                                    href="https://chat.whatsapp.com/Hbfx9TYmUeKIDcmbOBm535"
                                    target="_blank"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    Gabung Grup WhatsApp Murid Baru
                                </a>
                                <a
                                    href="{{ route('download-hasil-akhir-seleksi-tahap-2') }}"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Unduh Panduan Registrasi Murid Baru
                                </a>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    x-model="dontShowAgain"
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                >
                                <span class="text-sm text-gray-700">Jangan tampilkan lagi hari ini</span>
                            </label>
                            <button
                                @click="closeModal()"
                                type="button"
                                class="w-full rounded-lg bg-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                @endif

            @else
                {{-- ========== MODAL WELCOME LAMA (TAHAP 1) ========== --}}
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

                                    {{-- Section Pengumuman Wawancara Orang Tua --}}
                                    @if($pendaftaran->penerimaan->sesi_interview && $pendaftaran->penerimaan->sesi_interview != 0)
                                    <div class="rounded-lg bg-blue-50 border-2 border-blue-300 p-4 shadow-sm mt-3">
                                        <div class="flex items-start gap-3 mb-3">
                                            <div class="p-2 bg-blue-100 rounded-lg">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-blue-900 text-base mb-1">
                                                    PENGUMUMAN
                                                </h4>
                                                <p class="text-sm font-semibold text-blue-800">
                                                    SESI WAWANCARA ORANG TUA
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-sm text-blue-900 space-y-2">
                                            <p>Bapak Ibu Orang Tua / Wali Murid dari <strong>{{ $pendaftaran->dataPribadi->nama_lengkap }}</strong> dengan nomor pendaftaran <strong>{{ $pendaftaran->nomor_pendaftaran }}</strong>, kami mengundang kehadirannya untuk melaksanakan wawancara berkaitan dengan pendaftaran putra/putrinya di SMAN Taruna Nala Jawa Timur pada:</p>
                                            <p class="text-center font-bold text-blue-800 py-2 bg-blue-100 rounded-lg">{{ $pendaftaran->penerimaan->sesi_interview }}</p>
                                            <p>Wawancara hanya diwakili oleh 1 orangtua saja. Setelah sesi wawancara selesai, orangtua bersama casis dapat langsung meninggalkan area Sekolah.</p>
                                            <p class="mt-2">Terimakasih<br><strong>Panitia SPMB 2026</strong></p>
                                        </div>
                                    </div>
                                    @endif
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
                                            'icon' => '',
                                            'label' => 'Draft - Sedang Proses Pengisian',
                                            'desc' => 'Silakan lengkapi semua data pendaftaran Anda.'
                                        ],
                                        'submitted' => [
                                            'color' => 'yellow',
                                            'icon' => '',
                                            'label' => 'Menunggu Verifikasi',
                                            'desc' => 'Data Anda sedang dalam proses verifikasi oleh panitia.'
                                        ],
                                        'needs_revision' => [
                                            'color' => 'red',
                                            'icon' => '',
                                            'label' => 'Perlu Perbaikan',
                                            'desc' => 'Silakan perbaiki data sesuai catatan dari panitia.'
                                        ],
                                        'approved' => [
                                            'color' => 'green',
                                            'icon' => '',
                                            'label' => 'Disetujui',
                                            'desc' => 'Pendaftaran Anda telah disetujui. Tunggu pengumuman seleksi.'
                                        ],
                                        'rejected' => [
                                            'color' => 'gray',
                                            'icon' => '',
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
            @endif
        </div>
    </div>
</div>
