<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Ringkasan Status --}}
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-900/20">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-800">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Verifikasi Pendaftaran</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        No. Pendaftaran: <span class="font-mono font-bold">{{ $this->record->nomor_pendaftaran }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Info Cards Ringkasan --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Data Pribadi Card --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900">
                                <svg class="h-4 w-4 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Data Pribadi</h3>
                        </div>
                        <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $this->record->dataPribadi?->nama_lengkap ?? 'Nama' }}</p>
                            <p><span class="font-medium">NIK:</span> {{ $this->record->user?->nik ?? '-' }}</p>
                            <p><span class="font-medium">NISN:</span> {{ $this->record->user?->nisn ?? '-' }}</p>
                            <p>{{ $this->record->dataPribadi?->tempat_lahir ?? '-' }}, {{ $this->record->dataPribadi?->tanggal_lahir?->format('d/m/Y') ?? '-' }}</p>
                            <p>{{ $this->record->dataPribadi?->jenis_kelamin ?? '-' }} | {{ $this->record->dataPribadi?->agama ?? '-' }}</p>
                        </div>
                    </div>
                    @if($this->record->dataPribadi?->pas_foto)
                        <img src="{{ asset('storage/' . $this->record->dataPribadi->pas_foto) }}" 
                             alt="Pas Foto" 
                             class="h-24 w-20 rounded border-2 border-gray-300 object-cover dark:border-gray-600 flex-shrink-0 ml-3">
                    @else
                        <div class="h-24 w-20 rounded border-2 border-dashed border-gray-300 bg-gray-100 flex items-center justify-center dark:border-gray-600 dark:bg-gray-700 flex-shrink-0 ml-3">
                            <span class="text-xs text-gray-400">No Photo</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Data Orang Tua Card --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                        <svg class="h-4 w-4 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Data Orang Tua</h3>
                </div>
                <div class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                    <div class="border-b pb-2 dark:border-gray-700">
                        <p class="font-medium text-gray-900 dark:text-gray-100">👨 Ayah</p>
                        <p class="mt-0.5">{{ $this->record->dataOrangTua?->nama_ayah ?? '-' }}</p>
                        <p class="text-[10px] mt-0.5">
                            <span class="inline-block px-2 py-0.5 bg-blue-100 dark:bg-blue-900 rounded text-blue-800 dark:text-blue-200">
                                {{ $this->record->dataOrangTua?->status_ayah ?? 'Status' }}
                            </span>
                        </p>
                        <p class="text-[10px] mt-0.5">{{ $this->record->dataOrangTua?->pekerjaan_ayah ?? 'Pekerjaan' }}</p>
                    </div>
                    <div class="pt-1 border-b pb-2 dark:border-gray-700">
                        <p class="font-medium text-gray-900 dark:text-gray-100">👩 Ibu</p>
                        <p class="mt-0.5">{{ $this->record->dataOrangTua?->nama_ibu ?? '-' }}</p>
                        <p class="text-[10px] mt-0.5">
                            <span class="inline-block px-2 py-0.5 bg-pink-100 dark:bg-pink-900 rounded text-pink-800 dark:text-pink-200">
                                {{ $this->record->dataOrangTua?->status_ibu ?? 'Status' }}
                            </span>
                        </p>
                        <p class="text-[10px] mt-0.5">{{ $this->record->dataOrangTua?->pekerjaan_ibu ?? 'Pekerjaan' }}</p>
                    </div>
                    <div class="pt-1">
                        <p class="font-medium">📱 {{ $this->record->dataOrangTua?->no_hp_ortu ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Data Sekolah Card --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900">
                        <svg class="h-4 w-4 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sekolah Asal</h3>
                </div>
                <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                    <p class="font-medium">{{ $this->record->dataSekolahAsal?->nama_sekolah ?? '-' }}</p>
                    <p>Jenjang: {{ $this->record->dataSekolahAsal?->jenjang_sekolah ?? '-' }}</p>
                    <p>Status: {{ $this->record->dataSekolahAsal?->status_sekolah ?? '-' }}</p>
                    <p class="text-[10px] pt-1">{{ $this->record->dataSekolahAsal?->alamat_sekolah ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Nilai Raport Summary --}}
        @if($this->record->nilaiRaport->count() > 0)
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="h-4 w-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Ringkasan Nilai Raport ({{ $this->record->nilaiRaport->count() }} Semester)</h3>
            </div>
            <div class="grid grid-cols-2 gap-3 text-xs md:grid-cols-5">
                @foreach($this->record->nilaiRaport as $nilai)
                    <div class="rounded-lg border border-gray-100 bg-gradient-to-br from-gray-50 to-white p-3 dark:border-gray-700 dark:from-gray-800 dark:to-gray-800">
                        <div class="font-semibold text-gray-700 dark:text-gray-200">Semester {{ $nilai->semester }}</div>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Rata-rata:</span>
                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-bold text-green-700 dark:bg-green-900 dark:text-green-300">
                                {{ number_format((float) $nilai->rata_rata, 2) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Prestasi & Organisasi --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @if($this->record->prestasi->count() > 0)
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-2 mb-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900">
                        <svg class="h-4 w-4 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Prestasi ({{ $this->record->prestasi->count() }})</h3>
                </div>
                <div class="space-y-2 text-xs">
                    @foreach($this->record->prestasi as $index => $prestasi)
                        <div class="rounded-lg border border-gray-100 bg-yellow-50 p-2 dark:border-gray-700 dark:bg-yellow-900/10">
                            <div class="font-medium text-gray-700 dark:text-gray-200">{{ $index + 1 }}. {{ $prestasi->nama_prestasi }}</div>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="rounded bg-yellow-200 px-2 py-0.5 text-[10px] font-semibold text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                    {{ $prestasi->tingkat_prestasi }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @php
                $organisasiList = $this->record->organisasi instanceof \Illuminate\Database\Eloquent\Collection ? $this->record->organisasi : collect($this->record->organisasi ? [$this->record->organisasi] : []);
            @endphp
            @if($organisasiList->count() > 0)
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-2 mb-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900">
                        <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Organisasi ({{ $organisasiList->count() }})</h3>
                </div>
                <div class="space-y-2 text-xs">
                    @foreach($organisasiList as $org)
                    <div class="rounded-lg border border-gray-100 bg-indigo-50 p-3 dark:border-gray-700 dark:bg-indigo-900/10">
                        <div class="font-semibold text-gray-700 dark:text-gray-200">{{ $org->nama_organisasi }}</div>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-gray-600 dark:text-gray-400">Jabatan:</span>
                            <span class="rounded bg-indigo-200 px-2 py-0.5 text-xs font-semibold text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200">
                                {{ $org->jabatan }}{{ $org->periode_jabatan ? ' (' . $org->periode_jabatan . ')' : '' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Form Verifikasi --}}
        <form wire:submit="save">
            {{ $this->form }}
        </form>
    </div>
    
    {{-- Script untuk check reactive state --}}
    @script
    <script>
        // Livewire hook untuk update button state
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            succeed(({ snapshot, effect }) => {
                // Trigger re-render setelah form state berubah
                if (component.name === 'filament.verifikator.resources.verifikasi-resource.pages.verifikasi-pendaftaran') {
                    component.$wire.$refresh();
                }
            })
        })
    </script>
    @endscript
</x-filament-panels::page>