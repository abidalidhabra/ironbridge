<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HuntUserDetail extends Eloquent
{
    /** status ->  [tobestart, running, paused, completed] **/
    protected $fillable = [
    	'hunt_user_id',
		'location',
		'game_id',
		'game_variation_id',
        'target',
		'revealed_at',
		'finished_in',
        'status',
        'started_at',
        'ended_at',
        'radius',
        'hunt_clue_id',
        'skipped_at',
        'failures_at',
        'walked',
	];

    protected $attributes = [
        'revealed_at' => null,
        'started_at'  => null,
        'ended_at'    => null,
        'finished_in' => 0,
        'status'      => 'tobestart',
        'walked' => 0,
        // 'skipped_at'  => null,
        // 'failures_at' => [],
    ];

	protected $dates = [
        'revealed_at',
        'started_at',
        'ended_at',
        'skipped_at',
    ];

    public function game()
    {
    	return $this->belongsTo('App\Models\v1\Game','game_id');
    }

    public function game_variation(){
    	return $this->belongsTo('App\Models\v1\GameVariation','game_variation_id');    	
    }

    public function hunt_user(){
        return $this->belongsTo('App\Models\v2\HuntUser');     
    }

    public function hunt_clue()
    {
        return $this->belongsTo('App\Models\v1\HuntClue');
    }
}
