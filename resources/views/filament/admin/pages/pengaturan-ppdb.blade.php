<x-filament-panels::page>
    @php
        $isClosed = $this->isCurrentlyClosed();
        $closedDateText = $this->getCurrentClosedDateFormatted();
    @endphp

    <div @class([
        'rounded-xl border p-4 mb-6 flex items-center justify-between gap-4',
        'bg-red-50 border-red-200 text-red-900' => $isClosed,
        'bg-green-50 border-green-200 text-green-900' => ! $isClosed,
    ])>
        <div>
            <p class="text-sm font-semibold">
                Status Pendaftaran Saat Ini:
                <span class="uppercase">{{ $isClosed ? 'Ditutup' : 'Dibuka' }}</span>
            </p>
            <p class="text-xs mt-1 opacity-80">
                @if ($isClosed)
                    Pendaftar hanya dapat mengakses Dashboard dan melihat status/pengumuman.
                @elseif ($closedDateText)
                    Pendaftar dapat mendaftar dan mengisi formulir hingga {{ $closedDateText }}.
                @else
                    Pendaftar dapat mendaftar dan mengisi formulir tanpa batas tanggal penutupan.
                @endif
            </p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check">
                Simpan Pengaturan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
