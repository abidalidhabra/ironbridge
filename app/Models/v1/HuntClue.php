<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntClue extends Eloquent
{
    protected $fillable = [
        'hunt_complexity_id', 
        'location',
        'game_id',
        'game_variation_id',
        'est_completion',
    ];
}
