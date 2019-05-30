<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Avatar extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gender', 'skin_colors', 'hairs_colors', 'eyes_colors'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'skin_colors' 	=> 'array',
    //     'hairs_colors'  => 'array',
    //     'eyes_colors'   => 'array',
    // ];
}
