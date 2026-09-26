<?php

namespace App\States\Booking;

use App\Enums\BookingStatus;

class ConfirmedState implements BookingState
{
    public function confirm(): BookingState
    {
        throw new \LogicException('A confirmed booking cannot be confirmed again.');
    }

    public function cancel(): BookingState
    {
        return new CancelledState();
    }

    public function status(): BookingStatus
    {
        return BookingStatus::CONFIRMED;
    }
}
