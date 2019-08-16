<?php

namespace App\Models\v1;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class EventLevel extends Eloquent
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'game_variation_id', 'level', 'target', 'starts_at', 'ends_at', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'  	=> 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'soon', /** [soon, running, completed] **/
    ];

    public function event()
    {
        return $this->belongsTo('App\Models\v1\Event');
    }

    public function game_variation()
    {
        return $this->belongsTo('App\Models\v1\GameVariation','game_variation_id');
    }
}
