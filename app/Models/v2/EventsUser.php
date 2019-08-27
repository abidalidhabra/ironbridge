<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class EventsUser extends Eloquent
{
    /** status ->  [tobestart, eliminated, persisted] **/
	protected $fillable = [ 'user_id','event_id', 'completed_at' , 'attempts' ,'status'];

	protected $dates = [
		'completed_at'
	];

	protected $attributes = [
		'completed_at' => null,
        'status' => 'tobestart'
	];

	public function minigames()
	{
		return $this->hasMany('App\Models\v2\EventsMinigame');
	}

	public function event()
	{
		return $this->belongsTo('App\Models\v2\Event');
	}

	public function user()
    {
    	return $this->belongsTo('App\Models\v1\User','user_id');
    }
}
