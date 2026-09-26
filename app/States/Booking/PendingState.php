<?php

namespace App\States\Booking;

use App\Enums\BookingStatus;

class PendingState implements BookingState
{
    public function confirm(): BookingState
    {
        return new ConfirmedState();
    }

    public function cancel(): BookingState
    {
        return new CancelledState();
    }

    public function status(): BookingStatus
    {
        return BookingStatus::PENDING;
    }
}
