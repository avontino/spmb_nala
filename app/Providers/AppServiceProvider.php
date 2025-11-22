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
            Livewire::setUpdateRoute(function ($handle) {
                return \Route::post("2026/livewire/update", $handle)
                    ->middleware(['web']); // Ensure the 'web' middleware is applied
            });

            // 2. Configure the Livewire JavaScript Asset Route (Also necessary)
            Livewire::setScriptRoute(function ($handle) {
                return \Route::get("2026/livewire/livewire.js", $handle);
            });
        }
    }
}
