<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class EventsHuntUser extends Eloquent
{
    /** status ->  [participated, paused, running, completed, eliminated] **/

	protected $fillable = [ 'events_user_id', 'hunt_id' , 'started_at', 'ended_at', 'status', 'finished_in', 'complexity', 'hunt_complexity_id' ];

	protected $dates = [
		'started_at',
		'ended_at'
	];

	 protected $attributes = [
        'started_at'  => null,
        'ended_at'	  => null,
        'finished_in' => null,
        'status' 	  => 'participated',
    ];
}
