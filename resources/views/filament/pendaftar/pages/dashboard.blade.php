<x-filament-panels::page>
    @php
        $pendaftaran = \App\Models\Pendaftaran::where('user_id', auth()->id())->first();
    @endphp

    {{-- Default Dashboard Widgets --}}
    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetsData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
