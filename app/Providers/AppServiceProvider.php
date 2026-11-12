<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // force use app_url
        \URL::forceRootUrl(config('app.url'));

        // force livewire to add prefix to routes on production
        if (config('app.env') === 'production') {
            $prefix = env('LIVEWIRE_PATH_PREFIX', '2027');
            $prefix = $prefix ? trim($prefix, '/') . '/' : '';

            Livewire::setUpdateRoute(function ($handle) use ($prefix) {
                return \Route::post("{$prefix}livewire/update", $handle)
                    ->middleware(['web']); // Ensure the 'web' middleware is applied
            });

            // 2. Configure the Livewire JavaScript Asset Route (Also necessary)
            Livewire::setScriptRoute(function ($handle) use ($prefix) {
                return \Route::get("{$prefix}livewire/livewire.js", $handle);
            });
        }
    }
}
