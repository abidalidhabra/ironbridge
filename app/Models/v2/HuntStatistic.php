<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntStatistic extends Eloquent
{
    protected $fillable = [
    	'power_ratio',
    	'gold',
    	'skeleton_keys',
    	'boost_power_till',
    	'refreshable_distances',
    	'distances',
    	'freeze_till',
    	'chest_xp',
        'mg_change_charge'
    ];
}
