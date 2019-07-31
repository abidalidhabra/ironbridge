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
        'name', 'code', 'dialing_code', 'currency', 'currency_symbol', 'currency_full_name'
    ];

    public function cities()
    {
    	return $this->hasMnay('App\Models\v1\City');
    }

    public function plans()
    {
        return $this->hasMany('App\Models\v2\Plan');
    }
}
