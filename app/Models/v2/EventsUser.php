<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class EventsUser extends Eloquent
{
    /** status ->  [tobestart, closed, running, completed, eliminated] **/
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
}
