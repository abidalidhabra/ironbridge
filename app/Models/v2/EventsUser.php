<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class EventsUser extends Eloquent
{
	protected $fillable = [ 'event_id', 'completed_at'];

	protected $dates = [
		'completed_at'
	];
}
