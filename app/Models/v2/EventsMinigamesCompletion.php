<?php

namespace App\Models\v2;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class EventsMinigamesCompletion extends Eloquent
{
	protected $fillable = [ 'events_minigame_id' , 'completion_in', 'completion_score'];
}
