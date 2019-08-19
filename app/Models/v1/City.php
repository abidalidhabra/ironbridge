<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class City extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'country_id', 'timezone'
    ];

    public function country()
    {
        return $this->belongsTo('App\Models\v1\Country');
    }

    public function events()
    {
        return $this->hasMany('App\Models\v2\Event');
    }
}
