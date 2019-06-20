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
		'finished_in',
        'status'
	];

	protected $dates = [
        'revealed_at',
    ];

    public function game()
    {
    	return $this->belongsTo('App\Models\v1\Game','game_id');
    }

    public function game_variation(){
    	return $this->belongsTo('App\Models\v1\GameVariation','game_variation_id');    	
    }
}
