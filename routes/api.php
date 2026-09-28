<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::post('/bookings', [BookingController::class, 'store']);

Route::get('/trips', [TripController::class, 'index']);
Route::get('/trips/{trip}', [TripController::class, 'show']);
Route::get('/trips/{trip}/available-seats', [TripController::class, 'availableSeats']);
