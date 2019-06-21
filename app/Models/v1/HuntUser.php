<?php

namespace App\Models\v1;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntUser extends Eloquent
{
    protected $fillable = [
        'user_id',
		'hunt_id',
		'hunt_complexity_id',
		'valid',
        'status',
        'hunt_mode',
        'skeleton'
    ];

    public function hunt()
    {
        return $this->belongsTo('App\Models\v1\Hunt','hunt_id');
    }

    public function hunt_user_details(){
        return $this->hasMany('App\Models\v1\HuntUserDetail','hunt_user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\v1\User','user_id');
    }    

    public function hunt_complexities()
    {
        return $this->belongsTo('App\Models\v1\HuntComplexitie','hunt_complexity_id');
    }
}
