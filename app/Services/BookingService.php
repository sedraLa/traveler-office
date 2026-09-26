<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\States\Booking\CancelledState;
use App\States\Booking\ConfirmedState;
use App\States\Booking\PendingState;

class BookingService
{
    public function confirm(Booking $booking): Booking
    {
        $state = $this->resolveState($booking->status);
        $newState = $state->confirm();

        $booking->status = $newState->status();
        $booking->save();

        return $booking;
    }

    public function cancel(Booking $booking): Booking
    {
        $state = $this->resolveState($booking->status);
        $newState = $state->cancel();

        $booking->status = $newState->status();
        $booking->save();

        return $booking;
    }

    private function resolveState(BookingStatus $status): \App\States\Booking\BookingState
    {
        return match ($status) {
            BookingStatus::PENDING   => new PendingState(),
            BookingStatus::CONFIRMED => new ConfirmedState(),
            BookingStatus::CANCELLED => new CancelledState(),
        };
    }
}
