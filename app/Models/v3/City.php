<?php

namespace App\Models\v3;

use App\Models\v3\Country;
use App\Models\v3\State;
use App\Models\v3\Event;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class City extends Eloquent
{
    protected $fillable = ['name', 'country_id', 'state_id', 'timezone'];

	public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
