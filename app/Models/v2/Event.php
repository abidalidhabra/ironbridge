<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Event extends Eloquent
{


    // hunt_details [ name, city, province, country, lat, long, place_name, search_place_name]

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
    ];    

    protected $dates = [
        'starts_at',
        'ends_at',
        'map_reveal_date'
    ];


    public function city()
    {
        return $this->belongsTo('App\Models\v1\City','city_id');
    }
}

    