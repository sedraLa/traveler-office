<?php

namespace App\Services\Pricing;

class NormalPricingStrategy implements TicketPricingStrategy
{
    public function calculate(float $basePrice): float
    {
        return $basePrice;
    }
}
