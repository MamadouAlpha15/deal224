<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // Import Schema facade for database schema operations
use App\Models\Ad;
use App\Observers\AdObserver;
use Illuminate\Pagination\Paginator;

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
        Ad::observe(AdObserver::class);
       paginator::useBootstrapFive(); // Use Bootstrap 5 for pagination styling
        Schema::defaultStringLength(191); // Set default string length for database columns to avoid issues with older MySQL versions
    }
}
