<?php

namespace App\Providers;

use App\Services\Pricing\NormalPricingStrategy;
use App\Services\Pricing\TicketPricingStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TicketPricingStrategy::class, NormalPricingStrategy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
