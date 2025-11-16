import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Pendaftar/**/*.php',
        './resources/views/filament/pendaftar/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}