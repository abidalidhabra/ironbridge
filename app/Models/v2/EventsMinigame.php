<?php

namespace App\Models\v2;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class EventsMinigame extends Eloquent
{
	
	protected $fillable = ['events_user_id', 'from', 'to', 'game_info', 'variation_data', 'mini_games','day'];

	protected $dates = [
		'from',
		'to',
	];

	protected $appends = [
        'status',
    ];

    public function getStatusAttribute()
    {
    	if ($this->from <= now() && $this->to >= now()) {
			return 'running';
		}else{
			return 'closed';
		}
    }
}
