<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class EventMapTimeDelay extends Eloquent
{
    protected $fillable = [
    	'event_id',
    	'group_type',
		'rank',
		'start_rank',
		'end_rank',
		'map_time_delay',
	];    
}
