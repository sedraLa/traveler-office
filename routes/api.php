<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings/{booking}', [BookingController::class, 'show']);
Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm']);
Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

Route::get('/trips', [TripController::class, 'index']);
Route::get('/trips/{trip}', [TripController::class, 'show']);
Route::get('/trips/{trip}/available-seats', [TripController::class, 'availableSeats']);
