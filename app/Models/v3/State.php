<?php

namespace App\Models\v3;

use App\Models\v3\Country;
use App\Models\v3\Event;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class State extends Eloquent
{
    protected $fillable = ['name', 'country_id','code'];

	public function country()
    {
        return $this->belongsTo(Country::class);
    }

   
}
