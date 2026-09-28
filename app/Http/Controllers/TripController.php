<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;

class TripController extends Controller
{
    public function index(): JsonResponse
    {
        $activeStatuses = [BookingStatus::PENDING->value, BookingStatus::CONFIRMED->value];

        $trips = Trip::withCount([
            'seats as booked_seats_count' => function ($query) use ($activeStatuses) {
                $query->whereHas('bookings', function ($q) use ($activeStatuses) {
                    $q->whereIn('status', $activeStatuses);
                });
            },
        ])->get();

        $data = $trips->map(function (Trip $trip) {
            return [
                'id'              => $trip->id,
                'from_city'       => $trip->from_city,
                'to_city'         => $trip->to_city,
                'departure_at'    => $trip->departure_at,
                'trip_type'       => $trip->trip_type->value,
                'total_seats'     => $trip->total_seats,
                'base_price'      => $trip->base_price,
                'available_seats' => $trip->total_seats - $trip->booked_seats_count,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function show(Trip $trip): JsonResponse
    {
        $activeStatuses = [BookingStatus::PENDING->value, BookingStatus::CONFIRMED->value];

        $seats = $trip->seats()->with(['bookings' => function ($query) use ($activeStatuses) {
            $query->whereIn('status', $activeStatuses);
        }])->get();

        $bookedSeatIds = $seats->filter(fn($seat) => $seat->bookings->isNotEmpty())->pluck('id');

        $seatsData = $seats->map(fn($seat) => [
            'id'          => $seat->id,
            'seat_number' => $seat->seat_number,
            'available'   => !$bookedSeatIds->contains($seat->id),
        ]);

        return response()->json([
            'data' => [
                'id'              => $trip->id,
                'from_city'       => $trip->from_city,
                'to_city'         => $trip->to_city,
                'departure_at'    => $trip->departure_at,
                'trip_type'       => $trip->trip_type->value,
                'total_seats'     => $trip->total_seats,
                'base_price'      => $trip->base_price,
                'available_seats' => $trip->total_seats - $bookedSeatIds->count(),
                'seats'           => $seatsData,
            ],
        ]);
    }

    public function availableSeats(Trip $trip): JsonResponse
    {
        $activeStatuses = [BookingStatus::PENDING->value, BookingStatus::CONFIRMED->value];

        $availableSeats = $trip->seats()
            ->whereDoesntHave('bookings', function ($query) use ($activeStatuses) {
                $query->whereIn('status', $activeStatuses);
            })
            ->get(['id', 'seat_number']);

        return response()->json(['data' => $availableSeats]);
    }
}
