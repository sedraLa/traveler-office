<?php

namespace App\Services;

use App\Services\Pricing\TicketPricingStrategy;

class VipBookingService
{
    public function __construct(
        private TicketPricingStrategy $pricingStrategy
    ) {}

    public function calculate(float $basePrice): float
    {
        return $this->pricingStrategy->calculate($basePrice);
    }
}
