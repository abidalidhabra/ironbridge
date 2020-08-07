<?php

namespace App\Models\v3;

use App\Models\v3\City;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Country extends Eloquent
{
    protected $fillable = [
        'name', 'code', 'dialing_code', 'currency', 'currency_symbol', 'currency_full_name'
    ];

    public function cities()
    {
    	return $this->hasMany(City::class);
    }
}
