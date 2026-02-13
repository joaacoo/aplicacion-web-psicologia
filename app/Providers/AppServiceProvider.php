<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Register observers for activity logging
        \App\Models\ClinicalHistory::observe(\App\Observers\ClinicalHistoryObserver::class);
        \App\Models\Turno::observe(\App\Observers\TurnoObserver::class);
    }
}
