<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'wants_to_return' => 'boolean',
        'is_cancelled' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'leave_datetime', 'arrive_datetime',
        'return_leave_datetime', 'return_arrive_datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'msisdn', 'class_id', 'accommodation_id', 'start_destination_id', 'end_destination_id', 'leave_datetime', 'arrive_datetime', 'train_number',
        'wants_to_return', 'return_class_id', 'return_accommodation_id', 'return_start_destination_id', 'return_end_destination_id',
        'return_leave_datetime', 'return_arrive_datetime', 'return_train_number', 'payment_amount', 'number_of_passengers',
        'number_of_adults', 'number_of_children', 'number_of_infants', 'emergency_contact_name',
        'emergency_contact_phone', 'is_cancelled',
    ];

    /*
     *  Returns the booking class
     */
    public function class()
    {
        return $this->belongsTo('App\TravelClass', 'class_id');
    }

    /*
     *  Returns the booking accommodation
     */
    public function accommodation()
    {
        return $this->belongsTo('App\Accommodation', 'accommodation_id');
    }

    /*
     *  Returns the booking tickets
     */
    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'booking_id');
    }

    /*
     *  Returns the booking start destination
     */
    public function startDestination()
    {
        return $this->belongsTo('App\Destination', 'start_destination_id');
    }

    /*
     *  Returns the booking end destination
     */
    public function endDestination()
    {
        return $this->belongsTo('App\Destination', 'end_destination_id');
    }

    /*
     *  Returns the booking return class
     */
    public function returnClass()
    {
        return $this->belongsTo('App\TravelClass', 'return_class_id');
    }

    /*
     *  Returns the booking return accommodation
     */
    public function returnAccommodation()
    {
        return $this->belongsTo('App\Accommodation', 'return_accommodation_id');
    }

    /*
     *  Returns the booking return start destination
     */
    public function returnStartDestination()
    {
        return $this->belongsTo('App\Destination', 'return_start_destination_id');
    }

    /*
     *  Returns the booking return end destination
     */
    public function returnEndDestination()
    {
        return $this->belongsTo('App\Destination', 'return_end_destination_id');
    }

    public function setWantsToReturnAttribute($value)
    {
        $this->attributes['wants_to_return'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    public function setIsCancelledAttribute($value)
    {
        $this->attributes['is_cancelled'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    /*
     *  Scope the booking by the specified search queries
     */
    public function scopeSearchBooking($query, $searchQuery = [])
    {
        //  If we want to filter the start destination
        if (isset($searchQuery['from_id']) && !empty($searchQuery['from_id'])) {
            $query = $query->where('start_destination_id', '=', $searchQuery['from_id']);
        }

        //  If we want to filter the end destination
        if (isset($searchQuery['to_id']) && !empty($searchQuery['to_id'])) {
            $query = $query->where('end_destination_id', '=', $searchQuery['to_id']);
        }

        //  If we want to filter the end destination
        if (isset($searchQuery['leave_date']) && !empty($searchQuery['leave_date'])) {
            //  e.g where leave_datetime = '2020-12-31'
            $query = $query->whereDate('leave_datetime', '=', $searchQuery['leave_date']);
        }

        return $query;
    }
}
