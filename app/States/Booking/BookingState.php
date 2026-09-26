<?php

namespace App\States\Booking;

use App\Enums\BookingStatus;

interface BookingState
{
    public function confirm(): BookingState;
    public function cancel(): BookingState;
    public function status(): BookingStatus;
}
