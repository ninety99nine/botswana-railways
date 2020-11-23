<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Trip extends Model
{
    protected $with = ['from_destination', 'to_destination'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_available' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_destination_id', 'to_destination_id', 'class_id', 
        'adult_price', 'senior_price', 'child_price', 'infant_price',
        'leave_at', 'arrive_at', 'train_number', 'is_available'
    ];

    public function setIsAvailableAttribute($value)
    {
        $this->attributes['is_available'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    /*
     *  Returns the travel class accommodations
     */
    public function from_destination()
    {
        return $this->belongsTo('App\Destination', 'from_destination_id');
    }
    
    /*
     *  Returns the travel class accommodations
     */
    public function to_destination()
    {
        return $this->belongsTo('App\Destination', 'to_destination_id');
    }

    /*
     *  Scope the trip by the specified search queries
     */
    public function scopeSearchTrip($query, $searchQuery = [])
    {
        //  If we want to filter the start trip
        if( isset( $searchQuery['from_id'] ) && !empty( isset( $searchQuery['from_id'] ) ) ){

            $query = $query->whereHas('from_destination', function (Builder $query) use ($searchQuery) {
                        $query->where('id', $searchQuery['from_id']);
                    });
        }
        
        //  If we want to filter the end trip
        if( isset( $searchQuery['to_id'] ) && !empty( isset( $searchQuery['to_id'] ) ) ){

            $query = $query->whereHas('to_destination', function (Builder $query) use ($searchQuery) {
                        $query->where('id', $searchQuery['to_id']);
                    });
        }

        return $query;
    }

    /**
     * Format the arrive at time from "21:00:00" to "21:00"
     *
     * @param  string  $value
     * @return string
     */
    public function getArriveAtAttribute($value)
    {
        return substr($value, 0, 5);
    }

    /**
     * Format the leave at time from "21:00:00" to "21:00"
     *
     * @param  string  $value
     * @return string
     */
    public function getLeaveAtAttribute($value)
    {
        return substr($value, 0, 5);
    }

}
