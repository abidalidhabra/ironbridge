<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Event extends Eloquent
{
    
    /** status ->  [tobestart, running, closed, finished, terminated] **/
	protected $fillable = [
        'name',
        'type',
        'coin_type',
        'rejection_ratio',
        'winning_ratio',
        'city_id',
        'user_id',
        'fees',
        'starts_at',
        'ends_at',
        'event_days',
        'discount_details',
        'hunt_id',
        'map_reveal_date',
        'hunt_clues',
        'coin_number',
        'discount_till',
        'discount',
        'description',
        'attempts',
        'status'
    ];    

    protected $dates = [
        'starts_at',
        'ends_at',
        'map_reveal_date',
        'discount_till'
    ];

     protected $attributes = [
        'status' => 'tobestart',
    ];

    protected $appends = [
        'discount_amount',
        'discount_countdown',
        'play_countdown',
    ];

    public function city()
    {
        return $this->belongsTo('App\Models\v1\City','city_id');
    }

    public function prizes()
    {
        return $this->hasMany('App\Models\v2\EventsPrize','event_id');
    }

    public function participations()
    {
        return $this->hasMany('App\Models\v2\EventsUser');
    }

    public function getDiscountAmountAttribute()
    {
        return round($this->fees - ($this->fees * ($this->discount / 100)), 2);
    }

    public function getDiscountCountDownAttribute()
    {
        return ($this->discount_till > now())? $this->discount_till->diffInSeconds(): 0;
    }

    public function getPlayCountDownAttribute()
    {
        return ($this->starts_at > now())? $this->starts_at->diffInSeconds(): 0;
    }

     /**
     * Scope a query to only include upcoming events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        $query->where('starts_at', '>=', now());
    }

    /**
     * Scope a query to return events having requested city.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHavingCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }
}

    