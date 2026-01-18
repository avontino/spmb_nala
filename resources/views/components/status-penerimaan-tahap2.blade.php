@props([
    'penerimaanTahap2',
    'pendaftaran',
    'variant' => 'full' // 'full' untuk widget, 'compact' untuk modal
])

@php
    $namaCasis = $pendaftaran->dataPribadi?->nama_lengkap ?? '-';
    $namaSekolah = $pendaftaran->dataSekolahAsal?->nama_sekolah ?? '-';
    $nomorVa = $penerimaanTahap2->nomor_va ?? '-';
    $sesiRegistrasi = $penerimaanTahap2->sesi ?? '-';
    $statusTahap2 = $penerimaanTahap2->status;
@endphp

@if($statusTahap2 === 'lolos')
    {{-- LOLOS TAHAP 2 --}}
    <div class="rounded-2xl bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 p-6 shadow-lg">
        <div class="flex gap-3 mb-4 items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-green-800">
                    SELAMAT! LOLOS SELEKSI TAHAP 2
                </h3>
                <p class="text-sm text-green-700">
                    Nomor: <span class="font-semibold">{{ $pendaftaran->nomor_pendaftaran }}</span>
                </p>
            </div>
        </div>

        <div class="text-sm text-green-900 space-y-3">
            <p>
                Selamat <strong>{{ $namaCasis }}</strong> asal sekolah <strong>{{ $namaSekolah }}</strong>, Berdasarkan seleksi yang telah dilakukan Ananda dinyatakan <span class="font-bold text-green-600">Lolos</span> SPMB SMA Negeri Taruna Nala Jawa Timur. Mohon membaca Panduan Registrasi Murid Baru dengan seksama.
            </p>

            {{-- Informasi Registrasi --}}
            <div class="p-4 bg-white/70 border border-green-200 rounded-lg">
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
                Bagi casis yang tidak melaksanakan Registrasi hingga tanggal 21 januari 2026 Pukul 13.00 WIB, maka akan digantikan oleh casis lain yang berstatus sebagai cadangan sesuai dengan urutan cadangan. Jangan percaya kepada siapapun yang menawarkan jasa dengan mengatasnamakan Panitia SPMB SMANTARNALA. Proses SPMB hanya dilaksanakan melelui informasi resmi sekolah dengan nomor WA <a href="https://wa.me/6281313085908" target="blank" class="font-weight-500 text-primary-500">081313085908</a>.
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

        {{-- Action Buttons --}}
        <div class="mt-4 grid grid-cols-1 gap-3">
            <a href="https://chat.whatsapp.com/Hbfx9TYmUeKIDcmbOBm535"
                target="_blank"
                class="flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Gabung Grup WhatsApp Murid Baru
            </a>
            <a href="{{ asset('files/panduan-registrasi-murid-baru.pdf') }}"
                target="_blank"
                class="flex items-center justify-center gap-2 px-4 py-3 bg-white hover:bg-gray-50 text-gray-700 text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg border border-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Unduh Panduan Registrasi Murid Baru
            </a>
        </div>
    </div>

@elseif($statusTahap2 === 'cadangan')
    {{-- CADANGAN TAHAP 2 --}}
    <div class="rounded-2xl bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-300 p-6 shadow-lg">
        <div class="flex gap-3 mb-4 items-center">
            <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-yellow-800">
                    STATUS: CADANGAN SELEKSI TAHAP 2
                </h3>
                <p class="text-sm text-yellow-700">
                    Nomor: <span class="font-semibold">{{ $pendaftaran->nomor_pendaftaran }}</span>
                </p>
            </div>
        </div>

        <div class="text-sm text-gray-700 space-y-3">
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

        {{-- Action Buttons --}}
        <div class="mt-4 grid grid-cols-1 gap-3">
            <a href="{{ asset('files/panduan-registrasi-murid-baru.pdf') }}"
                target="_blank"
                class="flex items-center justify-center gap-2 px-4 py-3 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Unduh Panduan Registrasi Murid Baru
            </a>
        </div>
    </div>

@elseif($statusTahap2 === 'tidak_lolos')
    {{-- TIDAK LOLOS TAHAP 2 --}}
    <div class="rounded-2xl bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 p-6 shadow-lg">
        <div class="flex gap-3 mb-4 items-center">
            <div class="p-2 bg-red-100 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-red-800">
                    TIDAK LOLOS SELEKSI TAHAP 2
                </h3>
                <p class="text-sm text-red-700">
                    Nomor: <span class="font-semibold">{{ $pendaftaran->nomor_pendaftaran }}</span>
                </p>
            </div>
        </div>

        <div class="text-sm text-gray-700 space-y-3">
            <p>
                Mohon maaf, Berdasarkan seleksi yang telah dilakukan Ananda <strong>{{ $namaCasis }}</strong> dinyatakan <span class="font-bold text-red-600">tidak lolos</span> SPMB SMA Negeri Taruna Nala Jawa Timur. Tetap semangat, semoga bisa diterima di sekolah lain yang lebih baik.
            </p>
            <p class="text-gray-600 italic text-center">
                (Panitia SPMB SMANTARNALA Tahun 2026)
            </p>
        </div>
    </div>
@endif
