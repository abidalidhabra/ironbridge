<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Event extends Eloquent
{

	protected $fillable = [
        'name',
        'type',
        'coin_type',
        'rejection_ratio',
        'winning_ratio',
        'city_id',
        'fees',
        'starts_at',
        'ends_at',
        'mini_games',
        'discount_details',
        'hunt_id',
        'map_reveal_date',
        'hunt_clues',
        'coin_number',
        'discount_till',
        'discount',
        'description',
        'attempts'
    ];    

    protected $dates = [
        'starts_at',
        'ends_at',
        'map_reveal_date',
        'discount_till'
    ];


    public function city()
    {
        return $this->belongsTo('App\Models\v1\City','city_id');
    }

    public function prizes()
    {
        return $this->hasMany('App\Models\v2\Prize','event_id');
    }

    public function participants()
    {
        return $this->hasMany('App\Models\v1\User');
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

    