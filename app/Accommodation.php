<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
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
        'name', 'maximum_seats', 'class_id', 'is_available'
    ];

    public function setIsAvailableAttribute($value)
    {
        $this->attributes['is_available'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }
}

