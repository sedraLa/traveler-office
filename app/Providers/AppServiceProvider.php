<?php

namespace App\Providers;

use App\Services\NormalBookingService;
use App\Services\Pricing\NormalPricingStrategy;
use App\Services\Pricing\TicketPricingStrategy;
use App\Services\Pricing\VipPricingStrategy;
use App\Services\VipBookingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TicketPricingStrategy::class, NormalPricingStrategy::class);

        $this->app->when(NormalBookingService::class)
            ->needs(TicketPricingStrategy::class)
            ->give(NormalPricingStrategy::class);

        $this->app->when(VipBookingService::class)
            ->needs(TicketPricingStrategy::class)
            ->give(VipPricingStrategy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
