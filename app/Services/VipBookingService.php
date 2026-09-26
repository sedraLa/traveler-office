<?php

namespace App\Services;

use App\Services\Pricing\TicketPricingStrategy;

class VipBookingService
{
    public function __construct(
        private TicketPricingStrategy $pricingStrategy
    ) {}
}
