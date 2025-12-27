{{-- resources/views/filament/pendaftar/widgets/registration-closed-widget.blade.php --}}

<x-filament::widget>
    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-red-800">
                        Pendaftaran Telah Ditutup
                    </h3>
                    <p class="text-xs text-red-600 mt-0.5">
                        Periode berakhir: {{ $this->getClosedDate() }}
                        @if($this->getDaysSinceClosed() > 0)
                            ({{ $this->getDaysSinceClosed() }} hari lalu)
                        @endif
                    </p>
                </div>
            </div>
            <a href="#status-pendaftaran"
               onclick="document.getElementById('status-pendaftaran').scrollIntoView({behavior: 'smooth'}); return false;"
               class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition">
                Lihat Status
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
        </div>
    </div>
</x-filament::widget>
