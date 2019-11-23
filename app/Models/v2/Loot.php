<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Loot extends Eloquent
{
    protected $fillable = [
        'number',
        'complexity', 
        'gold_value',
        'min_range',
        'max_range',
        'widget_value',
        'widgets_order',
        'skeletons',
        'possibility',
        'reward_type',
        'status'
    ];
}
