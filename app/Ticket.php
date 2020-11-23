<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 'passenger_type', 'identity_type',
        'identity_no', 'reference_no', 'booking_id'
    ];

    /*
     *  Returns the booking class
     */
    public function class()
    {
        return $this->belongsTo('App\Booking', 'booking_id');
    }
}
