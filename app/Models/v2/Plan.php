<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Plan extends Eloquent
{
    protected $fillable = ['name', 'country_id', 'price', 'gold_value'];

    public function country()
    {
    	return $this->belongsTo('App\Models\v1\Country');
    }
}
