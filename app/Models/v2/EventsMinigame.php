<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class EventsMinigame extends Eloquent
{
	protected $fillable = ['events_user_id', 'from', 'to', 'game_info', 'variation_data'];
}
