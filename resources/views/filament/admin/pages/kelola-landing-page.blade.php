<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end gap-3">
            <x-filament::button
                tag="a"
                href="{{ url('/') }}"
                target="_blank"
                color="gray"
                icon="heroicon-o-eye"
            >
                Buka Landing Page
            </x-filament::button>

            <x-filament::button type="submit" icon="heroicon-o-check">
                Simpan Konten
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
