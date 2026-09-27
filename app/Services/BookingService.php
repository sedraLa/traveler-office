<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\TripType;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Seat;
use App\Models\Trip;
use App\States\Booking\CancelledState;
use App\States\Booking\ConfirmedState;
use App\States\Booking\PendingState;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private NormalBookingService $normalBookingService,
        private VipBookingService $vipBookingService
    ) {}

    public function createBooking(Customer $customer, Trip $trip, Seat $seat): Booking
    {
        return DB::transaction(function () use ($customer, $trip, $seat) {
            $seat = Seat::lockForUpdate()->findOrFail($seat->id);

            if ($seat->trip_id !== $trip->id) {
                throw new \LogicException('This seat does not belong to the given trip.');
            }

            $activeStatuses = [BookingStatus::PENDING->value, BookingStatus::CONFIRMED->value];

            $alreadyBooked = Booking::where('seat_id', $seat->id)
                ->where('trip_id', $trip->id)
                ->whereIn('status', $activeStatuses)
                ->exists();

            if ($alreadyBooked) {
                throw new \RuntimeException('This seat is already booked for this trip.');
            }

            $price = $trip->trip_type === TripType::VIP
                ? $this->vipBookingService->calculate($trip->base_price)
                : $this->normalBookingService->calculate($trip->base_price);

            return Booking::create([
                'customer_id' => $customer->id,
                'trip_id'     => $trip->id,
                'seat_id'     => $seat->id,
                'status'      => BookingStatus::PENDING,
                'price'       => $price,
            ]);
        });
    }

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
