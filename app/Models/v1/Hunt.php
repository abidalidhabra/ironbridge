<?php

namespace App\Models\v1;

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
    ];

    protected $attributes = [
        'fees' => 0,
        'replay_after' => 0,
    ];

    // protected $casts = [
    //     'boundaries_arr' => 'array',
    // ];
    public function hunt_complexities()
    {
        return $this->hasMany('App\Models\v1\HuntComplexitie','hunt_id');
    }
}
