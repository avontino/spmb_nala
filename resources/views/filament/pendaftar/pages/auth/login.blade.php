<x-filament-panels::page.simple>
    
    
        @if (filament()->hasRegistration())
        <x-slot name="subheading">
            <div class="text-center">
                Klik 
                <a href="{{ filament()->getRegistrationUrl() }}" class="text-primary-600 hover:text-primary-700 font-semibold">
                    Daftar disini
                </a> 
                Jika Belum Pernah Daftar
            </div>
        </x-slot>
    @endif
    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{-- Link Password Reset --}}
    @if (filament()->hasPasswordReset())
        <div class="text-center mt-4">
            <a href="{{ filament()->getRequestPasswordResetUrl() }}" 
               class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                Lupa password?
            </a>
        </div>
    @endif
</x-filament-panels::page.simple>