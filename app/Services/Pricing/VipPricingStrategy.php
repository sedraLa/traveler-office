<?php

namespace App\Services\Pricing;

class VipPricingStrategy implements TicketPricingStrategy
{
    public function calculate(float $basePrice): float
    {
        return $basePrice * 1.20;
    }
}
