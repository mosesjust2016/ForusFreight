<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;
use App\Models\Shipment;
use App\Observers\ShipmentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Mount Volt components
        Volt::mount(resource_path('views/livewire'));

        // Register model observers
        Shipment::observe(ShipmentObserver::class);
    }
}
