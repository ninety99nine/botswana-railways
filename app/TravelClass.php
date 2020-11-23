<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TravelClass extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'travel_classes';

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
        'name', 'is_available'
    ];

    public function setIsAvailableAttribute($value)
    {
        $this->attributes['is_available'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    /*
     *  Returns the travel class accommodations
     */
    public function accommodations()
    {
        return $this->hasMany('App\Accommodation', 'class_id');
    }

    /*
     *  Returns the travel class trips
     */
    public function trips()
    {
        return $this->hasMany('App\Trip', 'class_id');
    }

}
