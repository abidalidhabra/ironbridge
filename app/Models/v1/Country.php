<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Country extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'iso_code', 'un_code', 'dialing_code'
    ];

    public function cities()
    {
    	return $this->hasMnay('App\Models\v1\City');
    }
}
