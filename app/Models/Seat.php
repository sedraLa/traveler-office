<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['trip_id', 'seat_number'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
