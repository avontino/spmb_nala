{{-- resources/views/filament/pendaftar/widgets/revisi-notification-widget.blade.php --}}

<x-filament::widget>
    @php
        $pendaftaran = \App\Models\Pendaftaran::where('user_id', auth()->id())->first();
        $needsRevision = $pendaftaran && $pendaftaran->status === 'needs_revision';
        $latestVerifikasi = $pendaftaran?->latestVerifikasi;
    @endphp

    {{-- ✅ TAMBAH NULL CHECK: Hanya tampilkan jika needs_revision DAN ada verifikasi --}}
    @if($needsRevision && $latestVerifikasi)
        <div class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/20 border-2 border-red-500 rounded-lg">
            <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-1">
                    🔴 Pendaftaran Perlu Diperbaiki
                </h3>
                <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                    Verifikator telah mengirimkan permintaan perbaikan untuk beberapa bagian data Anda.
                    Silakan perbaiki data sesuai catatan di bawah ini.
                </p>
                
                {{-- ✅ TAMBAH NULL CHECK untuk catatan_revisi --}}
                @if($latestVerifikasi->catatan_revisi)
                    <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-red-200 dark:border-red-800">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                            📝 Catatan dari Verifikator:
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $latestVerifikasi->catatan_revisi }}</p>
                    </div>
                @endif
                
                {{-- ✅ Tampilkan section mana yang perlu diperbaiki --}}
                <div class="mt-3 space-y-2">
                    <p class="text-sm font-medium text-red-900 dark:text-red-100">
                        Bagian yang perlu diperbaiki:
                    </p>
                    <ul class="space-y-1 text-sm">
                        @if(!$latestVerifikasi->data_pribadi_ok)
                            <li class="flex items-center gap-2 text-red-700 dark:text-red-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span>Data Pribadi</span>
                            </li>
                        @endif
                        
                        @if(!$latestVerifikasi->data_orang_tua_ok)
                            <li class="flex items-center gap-2 text-red-700 dark:text-red-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span>Data Orang Tua</span>
                            </li>
                        @endif
                        
                        @if(!$latestVerifikasi->sekolah_asal_ok)
                            <li class="flex items-center gap-2 text-red-700 dark:text-red-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span>Data Sekolah Asal</span>
                            </li>
                        @endif
                        
                        @if(!$latestVerifikasi->nilai_raport_ok)
                            <li class="flex items-center gap-2 text-red-700 dark:text-red-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span>Nilai Raport</span>
                            </li>
                        @endif
                        
                        @if(!$latestVerifikasi->prestasi_organisasi_ok)
                            <li class="flex items-center gap-2 text-red-700 dark:text-red-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span>Prestasi & Organisasi</span>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <div class="mt-4">
                    <a href="{{ \App\Filament\Pendaftar\Resources\PendaftaranResource::getUrl('edit-data-pribadi', ['record' => $pendaftaran]) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Mulai Perbaiki Data
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-filament::widget>