<?php

namespace App\Models;

use App\Enums\TripType;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = ['from_city', 'to_city', 'departure_at', 'trip_type', 'total_seats', 'base_price'];

    protected $casts = [
        'trip_type' => TripType::class,
    ];

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
