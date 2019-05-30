<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class GameVariation extends Eloquent
{

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'user_id', 'completed_levels'
    ];
    
    public function game()
    {
    	return $this->belongsTo('App\Models\v1\Game');
    }
}
