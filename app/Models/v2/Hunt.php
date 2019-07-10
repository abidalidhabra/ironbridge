<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Hunt extends Eloquent
{
    protected $fillable = [
        '_id',
        'location',
        'name',
        'place_name',
        'boundaries_arr',
        'boundingbox',
        'city',
        'province',
        'country',
        'fees',
        'replay_after',
        'verified',
    ];

    protected $attributes = [
        'fees' => 0,
        'replay_after' => 0,
        'verified' => false,
    ];

    public function hunt_complexities()
    {
        return $this->hasMany('App\Models\v1\HuntComplexity','hunt_id');
    }

    public function hunt_users()
    {
        return $this->hasMany('App\Models\v2\HuntUser');
    }
}
