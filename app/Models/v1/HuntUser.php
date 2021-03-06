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
        'skeleton',
        'started_at',
        'ended_at',
        'est_completion',
        'complexity',
        'relic_id',
    ];

    protected $dates = [
        'revealed_at',
        'started_at',
        'ended_at',
    ];

    protected $attributes = [
        'est_completion' => 0
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
        return $this->belongsTo('App\Models\v1\HuntComplexity','hunt_complexity_id');
    }
}
