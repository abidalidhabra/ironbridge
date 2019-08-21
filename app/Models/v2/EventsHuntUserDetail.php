<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class EventsHuntUserDetail extends Eloquent
{
    /** status ->  [tobestart, running, paused, completed] **/
    /** radius ->  [distance in meters] **/

	protected $fillable = ['events_hunt_user_id' , 'revealed_at' , 'started_at' , 'ended_at' , 'finished_in' , 'status' , 'location' , 'game_id' , 'game_variation_id' , 'radius'];

	protected $dates = [
		'revealed_at',
		'started_at',
		'ended_at',
	];

	 protected $attributes = [
        'revealed_at' => null,
        'started_at'  => null,
        'ended_at'	  => null,
        'finished_in' => null,
        'status' 	  => 'tobestart',
    ];
}
