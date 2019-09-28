<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntReward extends Eloquent
{
    protected $fillable = [
        'complexity', 
        'gold_value',
        'min_range',
        'max_range',
        'widget_value',
        'widgets_order',
        'skeletons',
        'possibility'
    ];
}
