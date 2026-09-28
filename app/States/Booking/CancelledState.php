<?php

namespace App\States\Booking;

use App\Enums\BookingStatus;
use App\Exceptions\InvalidBookingStateException;

class CancelledState implements BookingState
{
    public function confirm(): BookingState
    {
        throw new InvalidBookingStateException();
    }

    public function cancel(): BookingState
    {
        throw new InvalidBookingStateException();
    }

    public function status(): BookingStatus
    {
        return BookingStatus::CANCELLED;
    }
}
