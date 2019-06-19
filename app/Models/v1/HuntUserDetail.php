<?php

namespace App\Models\v1;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntUserDetail extends Eloquent
{
    protected $fillable = [
    	'hunt_user_id',
		'location',
		'game_id',
		'game_variation_id',
		'est_completion',
		'revealed_at',
		'started_at',
		'finished_at'
	];

	protected $dates = [
        'revealed_at',
		'started_at',
		'finished_at',
    ];
}
