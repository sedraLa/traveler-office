<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
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
}
