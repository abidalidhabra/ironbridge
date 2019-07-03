<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntUser extends Eloquent
{

    /** status ->  [participated, running, completed] **/
    protected $fillable = [
        'user_id',
        'hunt_id',
        'complexity',
        'hunt_mode',
        'skeleton_keys',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $dates = [
        'started_at',
        'ended_at',
    ];

    protected $attributes = [
        'status'     => 'participated',
        'started_at' => null,
        'ended_at'   => null
    ];

    public function hunt()
    {
        return $this->belongsTo('App\Models\v1\Hunt','hunt_id');
    }

    public function hunt_user_details()
    {
        return $this->hasMany('App\Models\v2\HuntUserDetail');
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
