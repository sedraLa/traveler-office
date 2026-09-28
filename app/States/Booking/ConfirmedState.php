<?php

namespace App\States\Booking;

use App\Enums\BookingStatus;
use App\Exceptions\InvalidBookingStateException;

class ConfirmedState implements BookingState
{
    public function confirm(): BookingState
    {
        throw new InvalidBookingStateException();
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
