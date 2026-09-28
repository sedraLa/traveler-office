<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Seat;
use App\Models\Trip;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request, BookingService $bookingService): JsonResponse
    {
        $data = $request->validated();

        $customer = Customer::findOrFail($data['customer_id']);
        $trip     = Trip::findOrFail($data['trip_id']);
        $seat     = Seat::findOrFail($data['seat_id']);

        $booking = $bookingService->createBooking($customer, $trip, $seat);

        return response()->json($booking, 201);
    }

    public function show(Booking $booking): JsonResponse
    {
        $booking->load(['customer', 'trip', 'seat']);

        return response()->json(['data' => $booking]);
    }

    public function confirm(Booking $booking, BookingService $bookingService): JsonResponse
    {
        $booking = $bookingService->confirm($booking);

        return response()->json(['data' => $booking]);
    }

    public function cancel(Booking $booking, BookingService $bookingService): JsonResponse
    {
        $booking = $bookingService->cancel($booking);

        return response()->json(['data' => $booking]);
    }
}
