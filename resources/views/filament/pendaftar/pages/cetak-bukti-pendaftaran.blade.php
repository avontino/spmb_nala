<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Info --}}
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 rounded-lg p-6 border-2 border-blue-200 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Bukti Pendaftaran SPMB
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Nomor Pendaftaran: <span class="font-semibold text-blue-600">{{ $record->nomor_pendaftaran }}</span>
                    </p>
                </div>
                <div class="flex-shrink-0">
                    @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'submitted' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'needs_revision' => 'bg-orange-100 text-orange-800',
                        ];
                        $statusLabels = [
                            'draft' => 'Draft',
                            'submitted' => 'Menunggu Verifikasi',
                            'approved' => 'Diterima',
                            'rejected' => 'Ditolak',
                            'needs_revision' => 'Perlu Perbaikan',
                        ];
                        $statusIcons = [
                            'draft' => '📝',
                            'submitted' => '⏳',
                            'approved' => '✅',
                            'rejected' => '❌',
                            'needs_revision' => '🔄',
                        ];
                    @endphp
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800' }} font-semibold text-sm">
                        <span>{{ $statusIcons[$record->status] ?? '📄' }}</span>
                        <span>{{ $statusLabels[$record->status] ?? ucfirst($record->status) }}</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Preview Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 border-b-2 border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview Bukti Pendaftaran
                </h3>
            </div>

            <div class="p-8">
                {{-- ✅ BARIS 1: Pas Foto + Data Pribadi --}}
                <div class="flex gap-8 items-start mb-6 pb-6 border-b-2 border-gray-200">
                    {{-- Pas Foto --}}
                    <div class="flex-shrink-0">
                        @if($record->dataPribadi && $record->dataPribadi->pas_foto)
                            <div class="relative">
                                <img 
                                    src="{{ asset('storage/' . $record->dataPribadi->pas_foto) }}" 
                                    alt="Pas Foto"
                                    class="w-32 h-40 object-cover rounded-lg shadow-md border-4 border-blue-600">
                                <div class="absolute -bottom-2 -right-2 bg-green-500 text-white rounded-full p-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        @else
                            <div class="w-32 h-40 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-400">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Data Pribadi --}}
                    <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nama Lengkap</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white">
                                {{ $record->dataPribadi->nama_lengkap ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">NIK</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $record->user->nik ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">NISN</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $record->user->nisn ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tempat, Tanggal Lahir</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $record->dataPribadi->tempat_lahir ?? '-' }}, 
                                {{ $record->dataPribadi->tanggal_lahir ? \Carbon\Carbon::parse($record->dataPribadi->tanggal_lahir)->format('d F Y') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ✅ BARIS 2: Asal Sekolah & Nama Orang Tua --}}
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Asal Sekolah</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $record->dataSekolahAsal->nama_sekolah ?? '-' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nama Orang Tua/Wali</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $record->dataOrangTua->nama_ayah ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- ✅ Status & Timestamp --}}
                <div class="bg-blue-50 dark:bg-gray-900 rounded-lg p-4 border border-blue-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status Pendaftaran</p>
                            <p class="text-sm font-bold text-blue-600">
                                {{ $statusIcons[$record->status] ?? '📄' }} 
                                {{ $statusLabels[$record->status] ?? ucfirst($record->status) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal Submit</p>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                {{ $record->submitted_at ? $record->submitted_at->format('d F Y, H:i') : 'Belum disubmit' }}
                            </p>
                        </div>
                    </div>
                    @if($record->verified_at)
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <p class="text-xs text-gray-500">
                                <span class="font-semibold">Diverifikasi:</span> 
                                {{ $record->verified_at->format('d F Y, H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Instructions --}}
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        Petunjuk Penggunaan
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Klik tombol "📥 Download PDF" di atas untuk mengunduh bukti pendaftaran</li>
                            <li>Simpan file PDF dengan baik sebagai bukti pendaftaran Anda</li>
                            <li>Bawa bukti pendaftaran saat tes masuk atau verifikasi dokumen</li>
                            <li>Pastikan semua data sudah benar sebelum download</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>