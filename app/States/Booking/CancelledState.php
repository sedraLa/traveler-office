<?php

namespace App\States\Booking;

use App\Enums\BookingStatus;

class CancelledState implements BookingState
{
    public function confirm(): BookingState
    {
        throw new \LogicException('A cancelled booking cannot be confirmed.');
    }

    public function cancel(): BookingState
    {
        throw new \LogicException('A cancelled booking cannot be cancelled again.');
    }

    public function status(): BookingStatus
    {
        return BookingStatus::CANCELLED;
    }
}
