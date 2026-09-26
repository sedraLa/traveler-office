<?php

namespace App\Services\Pricing;

interface TicketPricingStrategy
{
    public function calculate(float $basePrice): float;
}
