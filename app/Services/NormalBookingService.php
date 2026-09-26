<?php

namespace App\Services;

use App\Services\Pricing\TicketPricingStrategy;

class NormalBookingService
{
    public function __construct(
        private TicketPricingStrategy $pricingStrategy
    ) {}
}
